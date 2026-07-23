<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\BestSellerCategoryCollection;
use Illuminate\Http\Request;
use App\Http\Resources\V2\ParntersCollection;
use App\Http\Resources\V2\BlogCollection;
use App\Http\Resources\V2\CategoryResource;
use App\Http\Resources\V2\ProductSingleCollection;
use App\Models\Bannars;
use App\Models\Partner;
use App\Models\BlogCategory;
use App\Models\Blog;
use App\Models\Category;
use App\Models\ClientReview;
use App\Models\Brand;
use App\Http\Resources\V2\BrandCollection;
use App\Http\Resources\V2\ClientReviewCollection;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Log;

class HomePageController extends Controller
{
    public function homePage(Request $request)
    {
        $partnersList = $this->partnersList($request);
        $blogs = $this->blogs();
        $reviews = $this->clientReviews();
        $categories = $this->categories_data();
        $bannersData = $this->getBannersData();

        $topPickCategories = Cache::remember('home-top-pick-categories', now()->addMinutes(30), function () {
            return Category::with(['parent.parent'])
                ->where('is_top_pick', 1)
                ->select([
                    'id',
                    'name',
                    'slug',
                    'parent_id',
                    'banner',
                    'icon',
                    'cover_image',
                    'color',
                    'lite_color',
                    'tagline',
                    'meta_title',
                    'meta_description',
                    'banner_alt',
                    'icon_alt',
                    'cover_image_alt'
                ])
                ->latest()
                ->limit(9)
                ->get()
                ->map(fn(Category $cat) => [
                    'id'               => $cat->id,
                    'name'             => $cat->name,
                    'slug'             => $cat->full_slug,
                    'banner'           => uploaded_asset($cat->banner),
                    'icon'             => uploaded_asset($cat->icon),
                    'cover_image'      => uploaded_asset($cat->cover_image),
                    'color'            => $cat->color ?? '',
                    'lite_color'       => $cat->lite_color ?? '',
                    'tagline'          => $cat->tagline ?? '',
                    'meta_title'       => $cat->meta_title,
                    'meta_description' => $cat->meta_description,
                    'banner_alt'       => $cat->banner_alt,
                    'icon_alt'         => $cat->icon_alt,
                    'cover_image_alt'  => $cat->cover_image_alt,
                ]);
        });

        $trendingProducts = Cache::remember('home-trending-products', now()->addMinutes(30), function () {
            return Product::with(['stocks', 'taxes', 'main_category:id,color,lite_color'])
                ->where('is_trending', 1)
                ->select([
                    'id',
                    'name',
                    'thumbnail_img',
                    'slug',
                    'unit_price',
                    'discount_start_date',
                    'discount_end_date',
                    'discount_type',
                    'discount',
                    'rating',
                    'product_code',
                    'pip_code',
                    'todays_deal',
                    'featured',
                ])
                ->latest()
                ->limit(9)
                ->get();
        });

        $bestSellerProducts = Cache::remember('home-best-seller-products', now()->addMinutes(30), function () {
            return Product::with(['stocks', 'taxes', 'main_category:id,color,lite_color'])
                ->where('best_seller', 1)
                ->select([
                    'id',
                    'name',
                    'thumbnail_img',
                    'slug',
                    'unit_price',
                    'discount_start_date',
                    'discount_end_date',
                    'discount_type',
                    'discount',
                    'rating',
                    'product_code',
                    'pip_code',
                    'todays_deal',
                    'featured',
                ])
                ->latest()
                ->limit(9)
                ->get();
        });

        $topPickProducts = Cache::remember('home-top-pick-products', now()->addMinutes(30), function () {
            return Product::with(['stocks', 'taxes', 'main_category:id,color,lite_color'])
                ->where('is_top_pick', 1)
                ->select([
                    'id',
                    'name',
                    'thumbnail_img',
                    'slug',
                    'unit_price',
                    'discount_start_date',
                    'discount_end_date',
                    'discount_type',
                    'discount',
                    'rating',
                    'product_code',
                    'pip_code',
                    'todays_deal',
                    'featured',
                ])
                ->latest()
                ->limit(9)
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => array_merge($categories, [
                'partners' => $partnersList,
                'blogs' => $blogs,
                'reviews' => $reviews,
                'trending_products' => new ProductSingleCollection($trendingProducts),
                'top_picks_categories' => $topPickCategories,
                'top_pick_products' => new ProductSingleCollection($topPickProducts),
                'banners' => $bannersData,
                'best_seller_products' => new ProductSingleCollection($bestSellerProducts),
            ]),
        ]);
    }

    public function partnersList(Request $request)
    {
        // Cache the full sorted collection — never cache a paginator (it freezes page 1)
        $allBrands = Cache::remember('all_brands_list', now()->addMinutes(30), function () {
            return Brand::without('brand_translations')
                ->select([
                    'id',
                    'name',
                    'logo',
                    'featured',
                    'order_level'
                ])
                ->orderBy('featured', 'desc')   // featured brands first
                ->orderBy('order_level', 'asc')         // then by admin-set order
                ->orderBy('name', 'asc')                // then alphabetically
                ->get();
        });

        // Filter by search keyword in-memory (no extra DB query)
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $lower = strtolower($search);
            $allBrands = $allBrands->filter(function ($brand) use ($lower) {
                return str_contains(strtolower($brand->name), $lower);
            })->values();
        }

        // Paginate in-memory from the (optionally filtered) collection
        $perPage  = (int) $request->input('per_page', 120);
        $page     = (int) $request->input('page', 1);
        $total    = $allBrands->count();
        $items    = $allBrands->forPage($page, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return new BrandCollection($paginator);
    }

    public function blogCategories()
    {
        $categories = BlogCategory::select('id', 'category_name', 'slug', 'created_at')->get();
        return response()->json([
            'success' => true,
            'count'   => $categories->count(),
            'data'    => $categories,
        ]);
    }

    public function blogs()
    {
        $blogs = Blog::with('category:id,category_name,slug')
            ->select([
                'id',
                'category_id',
                'title',
                'slug',
                'short_description',
                'description',
                'banner',
                'meta_title',
                'meta_img',
                'meta_description',
                'meta_keywords',
                'status',
                'created_at',
            ])
            ->get();

        return new BlogCollection($blogs);
    }

    public function clientReviews()
    {
        $reviews = Cache::remember('client_reviews', now()->addMinutes(60), function () {
            return ClientReview::select('id', 'name', 'role', 'image', 'rating', 'comment', 'created_at')
                ->orderBy('id', 'desc')
                ->get();
        });

        return new ClientReviewCollection($reviews);
    }

    /**
     * Returns a plain array (safe to cache and use with array_merge).
     */
    public function categories_data(): array
    {
        return Cache::remember('homepage_v2', now()->addMinutes(30), function () {

            $menu = Category::active()
                ->where('parent_id', 0)
                ->with([
                    'children:id,name,slug,parent_id,color,lite_color,tagline',
                    'children.children:id,name,slug,parent_id,color,lite_color,tagline',
                    'children.children.children:id,name,slug,parent_id,color,lite_color,tagline',
                ])
                ->select(
                    'id',
                    'name',
                    'slug',
                    'parent_id',
                    'banner',
                    'icon',
                    'color',
                    'lite_color',
                    'tagline',
                    'cover_image',
                    'meta_title',
                    'meta_description',
                    'banner_alt',
                    'icon_alt',
                    'cover_image_alt'
                )
                ->get();

            // $featured = Category::active()
            //     ->where('featured', 1)
            //     ->select('id', 'name', 'slug', 'cover_image', 'meta_title', 'meta_description', 'banner', 'icon',
            //              'color', 'lite_color', 'tagline', 'banner_alt', 'icon_alt', 'cover_image_alt')
            //     ->with('coverImage')
            //     ->limit(8)
            //     ->get();

            // $bestSellerCategories = Category::active()
            //     ->where('best_seller', 1)
            //     ->with(['bestSellerProducts'])
            //     ->select('id', 'name', 'slug', 'banner', 'icon', 'cover_image', 'color', 'lite_color', 'tagline')
            //     ->limit(10)
            //     ->get();

            // Return a plain array — never a Response object inside cache
            return [
                'menu_categories'        => CategoryResource::collection($menu),
                // 'featured_categories'    => CategoryResource::collection($featured),
                // 'best_seller_categories' => BestSellerCategoryCollection::collection($bestSellerCategories),
            ];
        });
    }

    /**
     * Returns a plain array of formatted banners (safe to embed in response).
     */
    public function getBannersData(): array
    {
        $grouped = Cache::remember('all_banners', now()->addMinutes(30), function () {
            return Bannars::whereIn('status', [1, 2, 5, 6])
                ->latest()
                ->get()
                ->groupBy('status');
        });

        $format = fn($items, string $order = 'desc') => ($items ?? collect())
            ->sortBy('id', SORT_REGULAR, $order === 'desc')
            ->map(fn(Bannars $b) => $this->formatBanner($b))
            ->values();

        return [
            'hero_banners' => $format($grouped[1] ?? collect(), 'asc'),
            'middle_banners' => $format($grouped[2] ?? collect()),
            'best_seller_banners' => $format($grouped[5] ?? collect()),
            'trending_banners' => $format($grouped[6] ?? collect()),
            // 'monthly_banner'      => $format($grouped[3] ?? collect()),
            // 'support_banners'     => $format($grouped[4] ?? collect()),
        ];
    }

    /**
     * Standalone API endpoint for banners (used by BannarController route).
     */
    public function getAllBanners()
    {
        return response()->json([
            'success' => true,
            'data'    => $this->getBannersData(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function formatBanner(Bannars $banner): array
    {
        $data = [
            'id'          => $banner->id,
            // 'banner_type' => $b->banner_type ?? 'simple',
            'image'       => uploaded_asset($banner->image),
            'background_image' => uploaded_asset($banner->background_image),
            'url'         => $banner->url,
            'created_at'  => $banner->created_at?->toDateTimeString(),
        ];

        // if (($b->banner_type ?? 'simple') === 'product') {
        //     return array_merge($base, [
        //         'sku'           => $b->sku,
        //         'product_title' => $b->product_title,
        //         'price'         => (float) $b->price,
        //         'vat'           => (float) $b->vat,
        //         'button_text'   => $b->button_text,
        //     ]);
        // }

        return array_merge($data, [
            'title'       => $banner->title,
            'description' => $banner->description,
        ]);
    }
}
