<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use App\Utility\CategoryUtility;
use App\Models\Product;
use App\Models\ProductStock;

class CategoryCollection extends ResourceCollection
{
    protected $parentCategory;
    protected $featuredCategories;
    protected array $filters;

    public function __construct($resource, $parentCategory = null, $featuredCategories = null, array $filters = [])
    {
        parent::__construct($resource);
        $this->parentCategory     = $parentCategory;
        $this->featuredCategories = $featuredCategories;
        $this->filters            = array_merge([
            'sort'              => 'relevance',
            'min'               => null,
            'max'               => null,
            'perPage'           => 12,
            'top_rated'         => false,
            'new_arrival'       => false,
            'filter_bestseller' => false,
            'brands'            => [],
        ], $filters);
    }

    public function toArray($request)
    {
        $paginatedProducts = collect();
        $brands            = collect();
        $priceRange        = ['min' => 0, 'max' => 0];

        if ($this->parentCategory) {
            $sort    = $this->filters['sort'];
            $min     = $this->filters['min'];
            $max     = $this->filters['max'];
            $perPage = (int) $this->filters['perPage'];
            $topRated         = (bool) $this->filters['top_rated'];
            $newArrival       = (bool) $this->filters['new_arrival'];
            $filterBestseller = (bool) $this->filters['filter_bestseller'];
            $brandIds         = (array) $this->filters['brands'];

            $catId = $this->parentCategory->id;

            // ── Base query — direct Product query avoids BelongsToMany ambiguity ──
            // Include products from this category AND all its child categories
            // $allCatIds = array_merge([$catId], CategoryUtility::children_ids($catId));
            $allCatIds = [$catId];

            $query = Product::query()
                ->select('products.*')
                ->whereIn('products.category_id', $allCatIds)
                ->where('products.published', 1)
                ->where('products.approved', 1)
                ->where('products.auction_product', 0)
                ->where('products.digital', 0);

            // Exclude wholesale if not activated
            if (!addon_is_activated('wholesale')) {
                $query->where('products.wholesale_product', 0);
            }

            // Vendor filter
            if (get_setting('vendor_system_activation') == 1) {
                $verifiedSellers = verified_sellers_id();
                $query->where(function ($q) use ($verifiedSellers) {
                    $q->where('products.added_by', 'admin')
                      ->orWhereIn('products.user_id', $verifiedSellers);
                });
            } else {
                $query->where('products.added_by', 'admin');
            }

            // ── Price range filter ────────────────────────────────────────────
            if (!is_null($min) || !is_null($max)) {
                $query->whereExists(function ($sub) use ($min, $max) {
                    $sub->selectRaw('1')
                        ->from('product_stocks')
                        ->whereColumn('product_stocks.product_id', 'products.id');
                    if (!is_null($min)) $sub->where('product_stocks.price', '>=', $min);
                    if (!is_null($max)) $sub->where('product_stocks.price', '<=', $max);
                });
            }

            // ── Feature flag filters ──────────────────────────────────────────
            if ($topRated)         $query->where('products.todays_deal', 1);
            if ($filterBestseller) $query->where('products.best_seller', 1);
            if ($newArrival)       $query->orderByDesc('products.created_at'); // handled in sort too

            // ── Brand filter ──────────────────────────────────────────────────
            if (!empty($brandIds)) {
                $query->whereIn('products.brand_id', $brandIds);
            }

            // ── Price range metadata BEFORE applying sort (unfiltered for slider) ─
            $allIds = (clone $query)->pluck('products.id');
            $priceData = ProductStock::whereIn('product_id', $allIds)
                ->selectRaw('FLOOR(MIN(price)) as min_price, CEIL(MAX(price)) as max_price')
                ->first();
            $priceRange = [
                'min' => (float) ($priceData->min_price ?? 0),
                'max' => (float) ($priceData->max_price ?? 0),
            ];

            // ── Sorting ───────────────────────────────────────────────────────
            match ($sort) {
                'price_asc'  => $query->orderBy(
                                    ProductStock::select('price')
                                        ->whereColumn('product_id', 'products.id')
                                        ->orderBy('price')->limit(1)
                                ),
                'price_desc' => $query->orderByDesc(
                                    ProductStock::select('price')
                                        ->whereColumn('product_id', 'products.id')
                                        ->orderByDesc('price')->limit(1)
                                ),
                'name_asc'   => $query->orderBy('products.name', 'asc'),
                'bestseller' => $query->orderByDesc('products.best_seller')
                                      ->orderByDesc('products.num_of_sale'),
                'rating'     => $query->orderByDesc('products.rating'),
                default      => $query->orderByDesc('products.created_at'),
            };

            // ── Eager-load relations needed by ProductSingleCollection ────────
            $query->with([
                'stocks'        => function ($q) use ($min, $max) {
                    // When price filter is active, only load stocks within that range
                    // so ProductSingleCollection shows the correct lowest in-range price
                    if (!is_null($min)) $q->where('price', '>=', $min);
                    if (!is_null($max)) $q->where('price', '<=', $max);
                    $q->orderBy('price');
                },
                'taxes',
                'main_category:id,name,slug,color,lite_color',
                'wishlists'     => fn($q) => $q->where('user_id', auth()->id() ?? 0)
                                              ->select('product_id', 'user_id'),
            ]);

            $paginatedProducts = $query->paginate($perPage)->withQueryString();

            // ── Brands in this result set ─────────────────────────────────────
            $brands = $paginatedProducts->isNotEmpty()
                ? $paginatedProducts->pluck('brand_id')
                    ->filter()->unique()
                    ->pipe(fn($ids) => \App\Models\Brand::whereIn('id', $ids)->get(['id', 'name']))
                : collect();
        }

        $productCollection = new ProductSingleCollection($paginatedProducts);

        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id'                 => $data->id,
                    'parent_id'          => $data->parent_id,
                    'name'               => $data->name,
                    'color'              => $data->color,
                    'lite_color'         => $data->lite_color ?? '',
                    'tagline'            => $data->tagline ?? '',
                    'short_description'  => $data->short_description ?? '',
                    'banner'             => uploaded_asset($data->banner) ?? '',
                    'icon'               => uploaded_asset($data->icon) ?? '',
                    'meta_title'         => $data->meta_title,
                    'meta_description'   => $data->meta_description,
                    'cover_image'        => uploaded_asset($data->cover_image) ?? '',
                    'slug'               => $data->slug,
                    'title'              => $data->title,
                    'description'        => $data->description,
                    'number_of_children' => CategoryUtility::get_immediate_children_count($data->id),
                    'links' => [
                        'products'       => route('api.products.category', $data->id),
                        'sub_categories' => route('subCategories.index', $data->id),
                    ],
                ];
            }),

            'products' => $paginatedProducts->isNotEmpty() ? [
                'data'  => $productCollection,
                'meta'  => [
                    'current_page' => $paginatedProducts->currentPage(),
                    'from'         => $paginatedProducts->firstItem(),
                    'last_page'    => $paginatedProducts->lastPage(),
                    'per_page'     => $paginatedProducts->perPage(),
                    'to'           => $paginatedProducts->lastItem(),
                    'total'        => $paginatedProducts->total(),
                ],
                'links' => [
                    'first' => $paginatedProducts->url(1),
                    'last'  => $paginatedProducts->url($paginatedProducts->lastPage()),
                    'prev'  => $paginatedProducts->previousPageUrl(),
                    'next'  => $paginatedProducts->nextPageUrl(),
                ],
            ] : [],

            'current_category' => $this->parentCategory ? [
                'id'                  => $this->parentCategory->id,
                'name'                => $this->parentCategory->name,
                'color'               => $this->parentCategory->color,
                'short_description'   => $this->parentCategory->short_description ?? '',
                'lite_color'          => $this->parentCategory->lite_color ?? '',
                'tagline'             => $this->parentCategory->tagline ?? '',
                'overview'            => $this->parentCategory->overview ?? null,
                'our_range'           => $this->parentCategory->our_range ?? null,
                'why_us'              => $this->parentCategory->why_us ?? null,
                'faqs'                => $this->parentCategory->faqs
                                            ? (is_string($this->parentCategory->faqs)
                                                ? json_decode($this->parentCategory->faqs, true)
                                                : $this->parentCategory->faqs)
                                            : null,
                'title'               => $this->parentCategory->title,
                'content_description' => $this->parentCategory->content_description ?? null,
                'meta_title'          => $this->parentCategory->meta_title,
                'meta_description'    => $this->parentCategory->meta_description,
                'description'         => $this->parentCategory->description,
                'slug'                => $this->parentCategory->slug,
                'parent_id'           => $this->parentCategory->parent_id,
                'banner'              => uploaded_asset($this->parentCategory->banner),
                'icon'                => uploaded_asset($this->parentCategory->icon),
                'cover_image'         => uploaded_asset($this->parentCategory->cover_image),
            ] : null,

            'featured_categories' => $this->featuredCategories
                ? $this->featuredCategories->map(function ($data) {
                    return [
                        'id'                 => $data->id,
                        'name'               => $data->name,
                        'slug'               => $data->slug,
                        'banner'             => uploaded_asset($data->banner) ?? '',
                        'icon'               => uploaded_asset($data->icon) ?? '',
                        'cover_image'        => uploaded_asset($data->cover_image) ?? '',
                        'number_of_children' => CategoryUtility::get_immediate_children_count($data->id),
                    ];
                })
                : [],

            'brands'         => $brands->map(fn($b) => ['id' => $b->id, 'name' => $b->name])->values(),
            'price_range'    => $priceRange,
            'active_filters' => [
                'sort'              => $this->filters['sort'],
                'min'               => $this->filters['min'],
                'max'               => $this->filters['max'],
                'top_rated'         => $this->filters['top_rated'],
                'new_arrival'       => $this->filters['new_arrival'],
                'filter_bestseller' => $this->filters['filter_bestseller'],
                'brands'            => $this->filters['brands'],
            ],
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status'  => 200,
        ];
    }
}
