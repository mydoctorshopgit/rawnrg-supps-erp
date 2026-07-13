<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\BrandCollection;
use App\Http\Resources\V2\ProductSingleCollection;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Utility\SearchUtility;
use Cache;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brand_query = Brand::query();
        if ($request->name != "" || $request->name != null) {
            $brand_query->where('name', 'like', '%' . $request->name . '%');
            SearchUtility::store($request->name);
        }
        return new BrandCollection($brand_query->paginate(10));
    }

    public function show(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        // ── Validate inputs ───────────────────────────────────────────────────
        $validated = $request->validate([
            'sort'             => 'nullable|in:relevance,price_asc,price_desc,name_asc,newest,bestseller,rating',
            'min'              => 'nullable|numeric|min:0',
            'max'              => 'nullable|numeric|min:0',
            'top_rated'        => 'nullable|boolean',
            'new_arrival'      => 'nullable|boolean',
            'filter_bestseller'=> 'nullable|boolean',
            'per_page'         => 'nullable|integer|min:1|max:100',
        ]);

        $sort        = $validated['sort']    ?? 'relevance';
        $min         = isset($validated['min']) ? (float) $validated['min'] : null;
        $max         = isset($validated['max']) ? (float) $validated['max'] : null;
        $perPage     = (int) ($validated['per_page'] ?? 20);

        // ── Base query — all published products for this brand ────────────────
        $query = Product::query()
            ->select('products.*')
            ->join('categories as mc', 'mc.id', '=', 'products.category_id')
            ->where('products.brand_id', $brand->id)
            ->where('products.published', 1)
            ->where('mc.status', 1)
            ->where('products.digital', 0);

        // ── Feature flag filters ──────────────────────────────────────────────
        if (!empty($validated['top_rated']))         $query->where('products.todays_deal', 1);
        if (!empty($validated['new_arrival']))        $query->orderByDesc('products.created_at');
        if (!empty($validated['filter_bestseller']))  $query->where('products.best_seller', 1);

        // ── Price range filter ────────────────────────────────────────────────
        if (!is_null($min) || !is_null($max)) {
            $query->whereExists(function ($sub) use ($min, $max) {
                $sub->selectRaw('1')
                    ->from('product_stocks')
                    ->whereColumn('product_stocks.product_id', 'products.id');
                if (!is_null($min)) $sub->where('product_stocks.price', '>=', $min);
                if (!is_null($max)) $sub->where('product_stocks.price', '<=', $max);
            });
        }

        // ── Get matching product IDs (for price range metadata) ──────────────
        $productIds = (clone $query)->pluck('products.id');

        // ── Apply sort directly to the product query ──────────────────────────
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

        // ── Paginate products directly ────────────────────────────────────────
        $paginatedProducts = $query
            ->with([
                'stocks'        => fn($q) => $q->when($min, fn($q) => $q->where('price', '>=', $min))
                                              ->when($max, fn($q) => $q->where('price', '<=', $max))
                                              ->orderBy('price'),
                'taxes',
                'brand:id,name',
                'main_category:id,name,slug,color,lite_color',
                'wishlists'     => fn($q) => $q->where('user_id', auth()->id() ?? 0)
                                              ->select('product_id', 'user_id'),
            ])
            ->paginate($perPage)
            ->withQueryString();

        // ── Price range metadata for the slider ───────────────────────────────
        $priceRange = ProductStock::whereIn('product_id', $productIds)
            ->selectRaw('FLOOR(MIN(price)) as min_price, CEIL(MAX(price)) as max_price')
            ->first();

        // ── Build response ────────────────────────────────────────────────────
        $collection = new ProductSingleCollection($paginatedProducts);

        return response()->json(array_merge(
            $collection->toArray($request),
            [
                'brand' => [
                    'id'          => $brand->id,
                    'name'        => $brand->getTranslation('name'),
                    'logo'        => uploaded_asset($brand->logo),
                    'is_featured' => (bool) $brand->featured,
                ],
                'meta' => [
                    'total'        => $paginatedProducts->total(),
                    'per_page'     => $paginatedProducts->perPage(),
                    'current_page' => $paginatedProducts->currentPage(),
                    'last_page'    => $paginatedProducts->lastPage(),
                ],
                'filters' => [
                    'price_range' => [
                        'min' => (float) ($priceRange->min_price ?? 0),
                        'max' => (float) ($priceRange->max_price ?? 0),
                    ],
                    'active' => [
                        'sort'              => $sort,
                        'min'               => $min,
                        'max'               => $max,
                        'top_rated'         => !empty($validated['top_rated']),
                        'new_arrival'       => !empty($validated['new_arrival']),
                        'filter_bestseller' => !empty($validated['filter_bestseller']),
                    ],
                ],
                'success' => true,
                'status'  => 200,
            ]
        ));
    }

    public function top()
    {
        return Cache::remember('app.top_brands', 86400, function () {
            return new BrandCollection(Brand::where('top', 1)->get());
        });
    }
}
