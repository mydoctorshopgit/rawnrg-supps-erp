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
        Log::info(auth()->user());

        $partnersList          = $this->partnersList($request);
        $blogs                 = $this->blogs();
        $reviews               = $this->clientReviews();
        $categories            = $this->categories_data(); // plain array
        $bannersData           = $this->getBannersData();  // plain array

        $trending_products     = Product::where('is_trending', 1)
            ->with([
                'stocks'        => fn($q) => $q->orderBy('price'),
                'taxes',
                'main_category:id,color,lite_color',
            ])
            ->latest()->limit(15)->get();

        $best_seller_products = Product::where('best_seller', 1)
            ->latest()
            ->limit(15)
            ->get();

        // $monthly_deal_products = Product::where('monthly_deal', 1)
        //                             ->with(['stocks' => fn($q) => $q->orderBy('price'), 'taxes'])
        //                             ->latest()->limit(10)->get();

        // $save_big_categories   = Category::where('save_big', 1)
        //                             ->with(['parent.parent']) // load up to 2 levels up for full_slug
        //                             ->select('id', 'name', 'slug', 'parent_id', 'banner', 'icon', 'cover_image', 'color',
        //                                      'lite_color', 'tagline', 'meta_title', 'meta_description',
        //                                      'banner_alt', 'icon_alt', 'cover_image_alt')
        //                             ->latest()->limit(3)->get()
        //                             ->map(fn(Category $cat) => [
        //                                 'id'               => $cat->id,
        //                                 'name'             => $cat->name,
        //                                 'slug'             => $cat->full_slug, // parent/child/subchild
        //                                 'banner'           => uploaded_asset($cat->banner),
        //                                 'icon'             => uploaded_asset($cat->icon),
        //                                 'cover_image'      => uploaded_asset($cat->cover_image),
        //                                 'color'            => $cat->color ?? '',
        //                                 'lite_color'       => $cat->lite_color ?? '',
        //                                 'tagline'          => $cat->tagline ?? '',
        //                                 'meta_title'       => $cat->meta_title,
        //                                 'meta_description' => $cat->meta_description,
        //                                 'banner_alt'       => $cat->banner_alt,
        //                                 'icon_alt'         => $cat->icon_alt,
        //                                 'cover_image_alt'  => $cat->cover_image_alt,
        //                             ]);

        return response()->json([
            'success' => true,
            'data'    => array_merge(
                $categories, // menu_categories, featured_categories, best_seller_categories
                [
                    'partners'              => $partnersList,
                    'blogs'                 => $blogs,
                    'reviews'               => $reviews,
                    'trending_products'     => new ProductSingleCollection($trending_products),
                    // 'monthly_deal_products' => new ProductSingleCollection($monthly_deal_products),
                    // 'save_big_categories'   => $save_big_categories, // already mapped with full_slug
                    'banners'               => $bannersData,
                    'best_seller_products' => new ProductSingleCollection($best_seller_products),
                ]
            ),
        ]);
    }

    public function partnersList(Request $request)
    {
        // Cache the full sorted collection — never cache a paginator (it freezes page 1)
        $allBrands = Cache::remember('all_brands_list', now()->addMinutes(30), function () {
            return Brand::orderBy('featured', 'desc')   // featured brands first
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
        $blogs = Blog::with('category')->paginate(10);
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
        $grouped = Cache::remember('all_banners', 300, function () {
            return Bannars::whereIn('status', [1])
                ->latest()
                ->get()
                ->groupBy('status');
        });

        $format = fn($items, string $order = 'desc') => ($items ?? collect())
            ->sortBy('id', SORT_REGULAR, $order === 'desc')
            ->map(fn(Bannars $b) => $this->formatBanner($b))
            ->values();

        return [
            'hero_banners'        => $format($grouped[1] ?? collect(), 'asc'),
            // 'best_seller_banners' => $format($grouped[2] ?? collect()),
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

    private function formatBanner(Bannars $b): array
    {
        $base = [
            'id'          => $b->id,
            'banner_type' => $b->banner_type ?? 'simple',
            'image'       => uploaded_asset($b->image),
            'background_image' => uploaded_asset($b->background_image),
            'url'         => $b->url,
            'created_at'  => $b->created_at?->toDateTimeString(),
        ];

        if (($b->banner_type ?? 'simple') === 'product') {
            return array_merge($base, [
                'sku'           => $b->sku,
                'product_title' => $b->product_title,
                'price'         => (float) $b->price,
                'vat'           => (float) $b->vat,
                'button_text'   => $b->button_text,
            ]);
        }

        // simple banner
        return array_merge($base, [
            'title'       => $b->title,
            'description' => $b->description,
        ]);
    }
}
