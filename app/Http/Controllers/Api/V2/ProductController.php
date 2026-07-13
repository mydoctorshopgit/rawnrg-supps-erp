<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\SearchProductCollection;
use Cache;
use App\Models\Shop;
use App\Models\Color;
use App\Models\Product;
use App\Models\Category;
use App\Models\FlashDeal;
use Illuminate\Http\Request;
use App\Utility\SearchUtility;
use App\Models\CustomerProduct;
use App\Utility\CategoryUtility;
use App\Http\Resources\V2\ProductCollection;
use App\Http\Resources\V2\CategoryCollection;
use App\Http\Resources\V2\CategoryResource;
use App\Http\Resources\V2\FlashDealCollection;
use App\Http\Resources\V2\ProductMiniCollection;
use App\Http\Resources\V2\ProductDetailCollection;
use App\Http\Resources\V2\DigitalProductDetailCollection;
use App\Http\Resources\V2\ClassifiedProductMiniCollection;
use App\Http\Resources\V2\ClassifiedProductDetailCollection;
use App\Http\Resources\V2\MainProductCollection;
use App\Http\Resources\V2\ProductSingleCollection;

use App\Models\ProductStock;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Cache as FacadesCache;
use Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::whereHas('categories', function ($query) {
            $query->where('status', 1);
        })->latest()->paginate(10);
        return new ProductMiniCollection($products);
    }

    public function show($id)
    {
        return new ProductDetailCollection(Product::where('id', $id)->get());
        // if (Product::findOrFail($id)->digital==0) {
        //     return new ProductDetailCollection(Product::where('id', $id)->get());
        // }elseif (Product::findOrFail($id)->digital==1) {
        //     return new DigitalProductDetailCollection(Product::where('id', $id)->get());
        // }
    }

    // public function admin()
    // {
    //     return new ProductCollection(Product::where('added_by', 'admin')->latest()->paginate(10));
    // }

    public function getPrice(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $str = '';
        $tax = 0;
        $quantity = 1;



        if ($request->has('quantity') && $request->quantity != null) {
            $quantity = $request->quantity;
        }

        if ($request->has('color') && $request->color != null) {
            $str = Color::where('code', '#' . $request->color)->first()->name;
        }

        $var_str = str_replace(',', '-', $request->variants);
        $var_str = str_replace(' ', '', $var_str);

        if ($var_str != "") {
            $temp_str = $str == "" ? $var_str : '-' . $var_str;
            $str .= $temp_str;
        }

        $product_stock = $product->stocks->where('variant', $str)->first();
        $price = $product_stock->price;


        if ($product->wholesale_product) {
            $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $quantity)->where('max_qty', '>=', $quantity)->first();
            if ($wholesalePrice) {
                $price = $wholesalePrice->price;
            }
        }

        $stock_qty = $product_stock->qty;
        $stock_txt = $product_stock->qty;
        $max_limit = $product_stock->qty;

        if ($stock_qty >= 1 && $product->min_qty <= $stock_qty) {
            $in_stock = 1;
        } else {
            $in_stock = 0;
        }

        //Product Stock Visibility
        if ($product->stock_visibility_state == 'text') {
            if ($stock_qty >= 1 && $product->min_qty < $stock_qty) {
                $stock_txt = translate('In Stock');
            } else {
                $stock_txt = translate('Out Of Stock');
            }
        }

        //discount calculation
        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        // taxes
        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }

        $price += $tax;

        return response()->json(

            [
                'result' => true,
                'data' => [
                    'price' => single_price($price * $quantity),
                    'stock' => $stock_qty,
                    'stock_txt' => $stock_txt,
                    'digital' => $product->digital,
                    'variant' => $str,
                    'variation' => $str,
                    'max_limit' => $max_limit,
                    'in_stock' => $in_stock,
                    'image' => $product_stock->image == null ? "" : uploaded_asset($product_stock->image)
                ]

            ]
        );
    }

    public function seller($id, Request $request)
    {
        $shop = Shop::findOrFail($id);
        $products = Product::whereHas('main_category', function ($query) {
            $query->where('status', 1);
        })->where('added_by', 'seller')->where('user_id', $shop->user_id);
        if ($request->name != "" || $request->name != null) {
            $products = $products->where('name', 'like', '%' . $request->name . '%');
        }
        $products->where('published', 1);
        return new ProductMiniCollection($products->latest()->paginate(10));
    }

    public function category($id, Request $request)
    {
        $category = Category::find($id);
        $products = $category->products()->physical();

        if ($request->name != "" || $request->name != null) {
            $products = $products->where('name', 'like', '%' . $request->name . '%');
        }

        return new ProductMiniCollection(filter_products($products)->latest()->paginate(10));
    }

public function single($id)
{
    $id = trim($id, '/');

    $withRelations = ['stocks', 'taxes', 'reviews.user', 'brand', 'main_category'];

    if (is_numeric($id)) {
        $product = Product::with($withRelations)
            ->whereHas('main_category', fn($q) => $q->where('status', 1))
            ->find($id);
    } else {
        // 1. Exact slug match
        $product = Product::with($withRelations)
            ->whereHas('main_category', fn($q) => $q->where('status', 1))
            ->where('slug', $id)
            ->first();

        // 2. Incoming $id may be a stock-level slug (product-slug-size-color-sku).
        //    Try progressively shorter prefixes (longest first) to find the base product slug.
        if (!$product) {
            $parts = explode('-', $id);
            for ($i = count($parts) - 1; $i >= 1; $i--) {
                $candidate = implode('-', array_slice($parts, 0, $i));
                $found = Product::with($withRelations)
                    ->whereHas('main_category', fn($q) => $q->where('status', 1))
                    ->where('slug', $candidate)
                    ->first();
                if ($found) {
                    $product = $found;
                    break;
                }
            }
        }
    }

    if (!$product) {
        return response()->json(['error' => 'Product not found'], 404);
    }

    // ✅ Collect all stocks
    $stocksArray = stock_price($product->stocks);

    // Try to match requested slug exactly first
    $selectedVariant = collect($stocksArray)->first(fn($stock) => $stock['slug'] === $id);

    if (!$selectedVariant) {
        // Strip only uppercase SKU suffix (e.g. -MFNP100-XS) from stock slugs, then compare.
        // Using uppercase-only pattern avoids stripping lowercase size words like -small, -x-small.
        $selectedVariant = collect($stocksArray)->first(function ($stock) use ($id) {
            $cleanStockSlug = preg_replace('/-[A-Z0-9\-]+$/', '', $stock['slug']);
            return $cleanStockSlug === $id;
        });
    }

    // Fallback: pick first stock
    if (!$selectedVariant) {
        $selectedVariant = $stocksArray[0] ?? null;
    }

    if (!$selectedVariant) {
        return response()->json(['error' => 'Product not found'], 404);
    }

    // Fetch related products (same category, exclude current)
    $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->with(['stocks', 'taxes', 'brand', 'main_category'])
        ->whereHas('main_category', fn($q) => $q->where('status', 1))
        ->where('published', 1)
        ->limit(10)
        ->get();

    // ✅ Pass the original requested slug to MainProductCollection so it can
    // match at the correct level (size-only or full variant).
    return new \App\Http\Resources\V2\MainProductCollection(
        collect([$product]),
        $id,
        $relatedProducts
    );
}
    private function findMatchingBaseSlug(string $requestedSlug): ?string
    {
        if (empty($requestedSlug)) {
            return null;
        }

        // Get all non-null slugs, sorted by length DESC (longest first)
        $baseSlugs = Product::query()
            ->whereNotNull('slug')
            ->pluck('slug')
            ->sortByDesc(fn($slug) => strlen($slug))
            ->values();

        foreach ($baseSlugs as $slug) {
            if (str_starts_with($requestedSlug, $slug)) {
                $remaining = substr($requestedSlug, strlen($slug));

                // Accept exact match or "-something"
                if ($remaining === '' || str_starts_with($remaining, '-')) {
                    return $slug;
                }
            }
        }

        // No match found
        return null;
    }

    public function subCategory($id, Request $request)
    {
        $cacheKey = "categories_sub_{$id}";
        $parent_category = Category::where('id', $id)->first();
        $data = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($id) {
            return Category::where('parent_id', $id)->get();
        });

        return new CategoryCollection($data, $parent_category);
    }
    public function subCategorySpecific(Request $request)
    {
        $cacheKey = 'categories_sub_specific';

        $names = [
            'Gloves',
            'Circumcisions',
            'Single Use Instruments',
            'Single Use Procedure Packs',
            'Diagnostics',
            'Holloware',
            'Paper Products',
            'Samplers & Sampling',
            'Hand Sanitisers'
        ];
        $parent_category = Category::where('name', $names[0])->first();
        $data = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($names) {
            return Category::whereIn('name', $names)->get();
        });

        return new CategoryCollection($data, $parent_category);
    }
    public function subSubCategory($id, Request $request)
    {
        $cacheKey = "categories_sub_sub_{$id}";
        $parent_category = Category::where('id', $id)->first();
        $data = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($id) {
            return Category::where('parent_id', $id)->get();
        });

        return new CategoryCollection($data, $parent_category);
    }
    public function all_product()
    {
        $data = Product::whereHas('categories', function ($query) {
            $query->where('status', 1);
        })->paginate(10);
        return new ProductMiniCollection($data);
    }

    // function productName()
    // {
    //     try {
    //         $products = Product::select('name', 'slug')->get();

    //         // Format to [{ label: name, slug: slug }]
    //         $formatted = $products->map(function ($product) {
    //             return [
    //                 'label' => $product->name,
    //                 'slug' => $product->slug,
    //             ];
    //         });

    //         return response()->json(['success' => true, 'data' => $formatted], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //     }
    // }


    public function nameList($type)
    {
        try {

            if ($type === 'product') {
                $products = Product::select('name', 'slug')->get();
                $formatted = $products->map(function ($product) {
                    return [
                        'label' => $product->name,
                        'slug' => $product->slug,
                    ];
                });
                return response()->json(['success' => true, 'data' => $formatted], 200);
            } elseif ($type === 'brand') {
                $brands = Brand::select('name', 'id')->get();
                $formatted = $brands->map(function ($brand) {
                    return [
                        'name' => $brand->name,
                        'id' => $brand->id,
                    ];
                });
                return response()->json(['success' => true, 'data' => $formatted], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid type parameter. Use ?type=product or ?type=brand'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function brand($id, Request $request)
    {
        $products = Product::where('brand_id', $id)->physical();
        if ($request->name != "" || $request->name != null) {
            $products = $products->where('name', 'like', '%' . $request->name . '%');
        }
        return new ProductMiniCollection(filter_products($products)->latest()->paginate(10));
    }



    public function todaysDeal()
    {
        // return Cache::remember('app.todays_deal', 86400, function () {
        $products = Product::whereHas('main_category', function ($query) {
            $query->where('status', 1);
        })->where('todays_deal', 1)->physical();
        return new ProductSingleCollection(filter_products($products)->limit(20)->latest()->get());
        // });
    }

    public function flashDeal()
    {
        return Cache::remember('app.flash_deals', 86400, function () {
            $flash_deals = FlashDeal::where('status', 1)->where('featured', 1)->where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')))->get();
            return new FlashDealCollection($flash_deals);
        });
    }

    public function featured()
    {
        $products = Product::whereHas('main_category', function ($query) {
            $query->where('status', 1);
        })->where('featured', 1)->physical();
        return new ProductSingleCollection(filter_products($products)->latest()->paginate(10));
    }

    public function inhouse($id = null)
    {
        if ($id) {
            $products = Product::whereHas('main_category', function ($query) {
                $query->where('status', 1);
            })->where('category_id', $id);
        } else {
            $products = Product::whereHas('main_category', function ($query) {
                $query->where('status', 1);
            })->where('added_by', 'admin');
        }

        // Ensure the query is executed
        return new ProductSingleCollection(filter_products($products)->latest()->paginate(12));
    }


    public function all_categories()
    {
        $cacheKey = 'categories_all_parent_0';
        $parent_category = Category::where('parent_id', 0)->where('status', 1)->first();
        $data = FacadesCache::remember($cacheKey, now()->addMinutes(60), function () {
            return Category::where('parent_id', 0)->where('status', 1)
                ->orderBy('name', 'asc') // change 'name' to your actual column
                ->get();
        });

        return new CategoryCollection($data, $parent_category);
    }


    public function all_categories_v2($path = null)
    {
        $slugs = $path ? explode('/', $path) : [];

        // Read sort/filter params — same values as search API
        $sort    = request()->input('sort', 'relevance');
        $min     = request()->filled('min')              ? (float) request()->input('min')     : null;
        $max     = request()->filled('max')              ? (float) request()->input('max')     : null;
        $perPage = (int) request()->input('per_page', 12);
        $filters = [
            'sort'              => $sort,
            'min'               => $min,
            'max'               => $max,
            'perPage'           => $perPage,
            'top_rated'         => filter_var(request()->input('top_rated', false), FILTER_VALIDATE_BOOLEAN),
            'new_arrival'       => filter_var(request()->input('new_arrival', false), FILTER_VALIDATE_BOOLEAN),
            'filter_bestseller' => filter_var(request()->input('filter_bestseller', false), FILTER_VALIDATE_BOOLEAN),
            'brands'            => request()->filled('brands')
                                    ? array_filter(explode(',', request()->input('brands')))
                                    : [],
        ];

        /**
         * ✅ CASE 1: ROOT (No path)
         */
        if (empty($slugs)) {

            $data = Cache::remember('categories_root_v2', now()->addMinutes(60), function () {

                return [
                    'tree'     => $this->getCategoriesTree(null),
                    'featured' => $this->getFeaturedCategories(null),
                    'parent'   => null
                ];
            });

            return new CategoryCollection($data['tree'], $data['parent'], $data['featured'], $filters);
        }

        /**
         * ✅ CASE 2: Check Product Slug
         */
        $lastSlug = end($slugs);

        $product = Product::where('slug', $lastSlug)
            ->whereHas('main_category', fn($q) => $q->where('status', 1))
            ->first();

        if ($product) {
            return new ProductSingleCollection(collect([$product]));
        }

        /**
         * ✅ CASE 3: Resolve Category Path
         */
        $parentCategory = $this->resolveCategoryPath($slugs);

        if (!$parentCategory) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        /**
         * ✅ CASE 4: Get Nested Data
         * Note: tree/featured are cached; products are NOT cached (sort/filter varies per request)
         */
        $cacheKey = 'categories_path_' . md5($path) . '_v2';

        $data = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($parentCategory) {

            return [
                'children' => $this->getCategoriesTree($parentCategory->id),
                'featured' => $this->getFeaturedCategories($parentCategory->id),
                'parent'   => $parentCategory
            ];
        });

        return new CategoryCollection($data['children'], $data['parent'], $data['featured'], $filters);
    }

    /**
     * ✅ Get Full Nested Tree
     */
    private function getCategoriesTree($parentId = null)
    {
        return Category::where('status', 1)
            ->when($parentId, fn($q) => $q->where('parent_id', $parentId))
            ->when(!$parentId, fn($q) => $q->where('parent_id', 0))
            ->with('childrenRecursive') // 🔥 infinite nesting
            ->orderBy('order_level', 'desc')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * ✅ Get Featured Nested Tree
     */
    private function getFeaturedCategories($parentId = null)
    {
        return Category::where('status', 1)
            ->where('featured', 1)
            ->when($parentId, fn($q) => $q->where('parent_id', $parentId))
            ->when(!$parentId, fn($q) => $q->where('parent_id', 0))
            ->with('childrenRecursive')
            ->orderBy('order_level', 'desc')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * ✅ Resolve Slug Path → Category
     */
    private function resolveCategoryPath(array $slugs)
    {
        $parent = null;

        foreach ($slugs as $slug) {

            $parent = Category::where('slug', $slug)
                ->where('status', 1)
                ->when($parent, fn($q) => $q->where('parent_id', $parent->id))
                ->when(!$parent, fn($q) => $q->where('parent_id', 0))
                ->first();

            if (!$parent) return null;
        }

        return $parent;
    }

    public function categories_data()
    {
        return Cache::remember('homepage_v1', now()->addMinutes(30), function () {

            $menu = Category::active()
                ->where('parent_id', 0)
                ->with(['children:id,name,slug,parent_id'])
                ->select('id', 'name', 'slug', 'parent_id')
                ->get();

            $featured = Category::active()
                ->where('featured', 1)
                ->select('id', 'name', 'slug', 'cover_image')
                ->with('coverImage')
                ->limit(8)
                ->get();

            $bestSellerCategories = Category::active()
                ->where('best_seller', 1)
                ->with(['products' => function ($q) {
                    $q->where('best_seller', 1)
                        ->latest()
                        ->limit(10);
                }])
                ->limit(10)
                ->get();

            $trending_products = Product::where('is_trending', 1)->latest()->limit(10)->get();
            $monthly_deal_products = Product::where('monthly_deal', 1)->latest()->limit(10)->get();

            return response()->json([
                'menu_categories' => CategoryResource::collection($menu),
                'featured_categories' => CategoryResource::collection($featured),
                'best_seller_categories' => CategoryResource::collection($bestSellerCategories),

                'trending_products' => new ProductSingleCollection($trending_products),
                'monthly_deal_products' => new ProductSingleCollection($monthly_deal_products),
            ]);
        });
    }
    /**
     * Resolve category based on full path (parent → child → subchild).
     */
    private function resolveCategoryByPath($sub_category, $sub_sub_category, $sub_sub_sub_category)
    {
        $query = Category::query()->where('status', 1);

        // Start from top-level parent
        if ($sub_category) {
            $parent = Category::where('slug', $sub_category)
                ->where('status', 1)
                ->where('parent_id', 0)
                ->first();

            if (!$parent) {
                return null;
            }

            // Go one level deeper if provided
            if ($sub_sub_category) {
                $parent = Category::where('slug', $sub_sub_category)
                    ->where('status', 1)
                    ->where('parent_id', $parent->id)
                    ->first();

                if (!$parent) {
                    return null;
                }

                // One more level deeper if provided
                if ($sub_sub_sub_category) {
                    $parent = Category::where('slug', $sub_sub_sub_category)
                        ->where('status', 1)
                        ->where('parent_id', $parent->id)
                        ->first();
                }
            }

            return $parent;
        }

        return null;
    }






    public function digital()
    {
        $products = Product::digital();
        return new (filter_products($products)->latest()->paginate(10));
    }

    public function bestSeller()
    {
        // return Cache::remember('app.best_selling_products', 86400, function () {
        $products = Product::whereHas('main_category', function ($query) {
            $query->where('status', 1);
        })->orderBy('num_of_sale', 'desc')->physical();
        return new ProductMiniCollection(filter_products($products)->limit(20)->get());
        // });
    }

    public function related($id)
    {
        // return Cache::remember("app.related_products-$id", 86400, function () use ($id) {
        $product = Product::find($id);
        $products = Product::where('category_id', $product->category_id)->where('id', '!=', $id)->physical();
        return new ProductMiniCollection(filter_products($products)->limit(10)->get());

        // });
    }

    public function topFromSeller($id)
    {
        // return Cache::remember("app.top_from_this_seller_products-$id", 86400, function () use ($id) {
        $product = Product::find($id);
        $products = Product::where('user_id', $product->user_id)->orderBy('num_of_sale', 'desc')->physical();
        return new ProductMiniCollection(filter_products($products)->limit(10)->get());
        // });
    }


    public function search(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'nullable|string|max:200',
            'min'             => 'nullable|numeric|min:0',
            'max'             => 'nullable|numeric|min:0',
            'brands'          => 'nullable|string',
            'categories'      => 'nullable|string',
            'top_rated'       => 'nullable|boolean',
            'new_arrival'     => 'nullable|boolean',
            'bestseller' => 'nullable|boolean', // filter: only show best_seller=1 products
            'sort'            => 'nullable|in:relevance,price_asc,price_desc,name_asc,newest,bestseller,rating',
            'per_page'        => 'nullable|integer|min:1|max:50',
        ]);

        $name        = trim((string) ($validated['name'] ?? ''));
        $min         = isset($validated['min']) ? (float) $validated['min'] : null;
        $max         = isset($validated['max']) ? (float) $validated['max'] : null;
        $brandIds    = array_filter(explode(',', (string) ($validated['brands'] ?? '')));
        $categoryIds = array_filter(explode(',', (string) ($validated['categories'] ?? '')));
        $perPage     = (int) ($validated['per_page'] ?? 12);
        $sort        = $validated['sort'] ?? 'newest';

        // ── Base: JOIN instead of whereHas (avoids correlated subqueries) ────────
        $query = Product::query()
            ->select('products.*')
            ->join('categories as mc', 'mc.id', '=', 'products.category_id')
            ->where('products.published', 1)
            ->where('mc.status', 1)
            ->where('products.digital', 0);

        // ── Keyword: search name + product_code directly on products table ────────
        // SKU search uses a single LEFT JOIN instead of whereHas subquery
        if ($name !== '') {
            $query->leftJoin('product_stocks as sku_search', function ($join) use ($name) {
                $join->on('sku_search.product_id', '=', 'products.id')
                     ->where('sku_search.sku', 'like', "%{$name}%");
            });

            $query->where(function ($q) use ($name) {
                $q->where('products.name', 'like', "%{$name}%")
                  ->orWhere('products.product_code', 'like', "%{$name}%")
                  ->orWhere('products.tags', 'like', "%{$name}%")
                  ->orWhereNotNull('sku_search.id');
            });

            $query->groupBy('products.id');
        }

        // ── Brand filter ─────────────────────────────────────────────────────────
        if (!empty($brandIds)) {
            $query->whereIn('products.brand_id', $brandIds);
        }

        // ── Category filter — cache the tree expansion ────────────────────────────
        if (!empty($categoryIds)) {
            $expandedIds = Cache::remember(
                'cat_tree_' . implode('_', $categoryIds),
                now()->addMinutes(60),
                function () use ($categoryIds) {
                    $all = $categoryIds;
                    foreach ($categoryIds as $cid) {
                        $all = array_merge($all, CategoryUtility::children_ids($cid));
                    }
                    return array_unique($all);
                }
            );
            $query->whereIn('products.category_id', $expandedIds);
        }

        // ── Feature flags ─────────────────────────────────────────────────────────
        if (!empty($validated['top_rated']))         $query->where('products.todays_deal', 1);
        if (!empty($validated['bestseller'])) $query->where('products.best_seller', 1);

        // ── Price range — filter products that have at least one stock in range ────
        if (!is_null($min) || !is_null($max)) {
            $query->whereExists(function ($sub) use ($min, $max) {
                $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('product_stocks')
                    ->whereColumn('product_stocks.product_id', 'products.id');
                if (!is_null($min)) $sub->where('product_stocks.price', '>=', $min);
                if (!is_null($max)) $sub->where('product_stocks.price', '<=', $max);
            });
        }

        // ── Eager load — constrain stocks to price range when filtering ───────────
        $query->with([
            'stocks' => function ($q) use ($min, $max, $name) {
                // When price range is active, only load stocks within that range
                if (!is_null($min)) $q->where('price', '>=', $min);
                if (!is_null($max)) $q->where('price', '<=', $max);
                $q->orderBy('order')->orderBy('id');
            },
            'taxes',
            'brand:id,name',
            'main_category:id,name,slug',
            'wishlists' => fn($q) => $q->where('user_id', auth()->id() ?? 0)
                                       ->select('product_id', 'user_id'),
        ]);

        // ── Get all matching product IDs in sort order ────────────────────────────
        $productIds = (clone $query)->pluck('products.id');

        // ── Build stock-level query that respects the product sort order ──────────
        $stockQuery = ProductStock::query()
            ->whereIn('product_id', $productIds);

        if (!is_null($min)) $stockQuery->where('price', '>=', $min);
        if (!is_null($max)) $stockQuery->where('price', '<=', $max);

        // If name matches a SKU, only show those stocks; otherwise show all stocks
        if ($name !== '') {
            $nameMatchIds = Product::whereIn('id', $productIds)
                ->where(function ($pq) use ($name) {
                    $pq->where('name', 'like', "%{$name}%")
                       ->orWhere('product_code', 'like', "%{$name}%")
                       ->orWhere('tags', 'like', "%{$name}%");
                })->pluck('id');

            $stockQuery->where(function ($q) use ($name, $nameMatchIds) {
                $q->whereIn('product_id', $nameMatchIds)
                  ->orWhere('sku', 'like', "%{$name}%");
            });
        }

        // Apply the same sort logic to the stock query so pagination preserves order
        match ($sort) {
            'price_asc'  => $stockQuery->orderBy('price', 'asc')
                                       ->orderBy('product_id')
                                       ->orderBy('id'),
            'price_desc' => $stockQuery->orderBy('price', 'desc')
                                       ->orderBy('product_id')
                                       ->orderBy('id'),
            'name_asc'   => $stockQuery->join('products as p_sort', 'p_sort.id', '=', 'product_stocks.product_id')
                                       ->orderBy('p_sort.name', 'asc')
                                       ->orderBy('product_stocks.order')
                                       ->orderBy('product_stocks.id')
                                       ->select('product_stocks.*'),
            'bestseller' => $stockQuery->join('products as p_sort', 'p_sort.id', '=', 'product_stocks.product_id')
                                       ->orderByDesc('p_sort.best_seller')
                                       ->orderByDesc('p_sort.num_of_sale')
                                       ->orderBy('product_stocks.order')
                                       ->orderBy('product_stocks.id')
                                       ->select('product_stocks.*'),
            'rating'     => $stockQuery->join('products as p_sort', 'p_sort.id', '=', 'product_stocks.product_id')
                                       ->orderByDesc('p_sort.rating')
                                       ->orderBy('product_stocks.order')
                                       ->orderBy('product_stocks.id')
                                       ->select('product_stocks.*'),
            // relevance / newest — preserve product created_at order
            default      => $stockQuery->join('products as p_sort', 'p_sort.id', '=', 'product_stocks.product_id')
                                       ->orderByDesc('p_sort.created_at')
                                       ->orderBy('product_stocks.order')
                                       ->orderBy('product_stocks.id')
                                       ->select('product_stocks.*'),
        };

        $paginatedStocks = $stockQuery->paginate($perPage)->withQueryString();

        // ── Eager-load product data onto each stock ───────────────────────────────
        $stockProductIds = $paginatedStocks->pluck('product_id')->unique();

        $productsMap = Product::whereIn('id', $stockProductIds)
            ->with([
                'taxes',
                'brand:id,name',
                'main_category:id,name,slug',
                'wishlists' => fn($q) => $q->where('user_id', auth()->id() ?? 0)
                                           ->select('product_id', 'user_id'),
            ])
            ->get()
            ->keyBy('id');

        foreach ($paginatedStocks as $stock) {
            $stock->setRelation('product', $productsMap->get($stock->product_id));
        }

        // ── Filter metadata ───────────────────────────────────────────────────────
        $availableBrands = \App\Models\Brand::whereIn(
            'id',
            Product::whereIn('id', $stockProductIds)->whereNotNull('brand_id')->pluck('brand_id')
        )->get(['id', 'name']);

        $priceRange = ProductStock::whereIn('product_id', $productIds)
            ->selectRaw('FLOOR(MIN(price)) as min_price, CEIL(MAX(price)) as max_price')
            ->first();

        return (new \App\Http\Resources\SearchProductCollection($paginatedStocks, $name))
            ->additional([
                'meta' => [
                    'query'        => $name ?: null,
                    'total'        => $paginatedStocks->total(),
                    'per_page'     => $paginatedStocks->perPage(),
                    'current_page' => $paginatedStocks->currentPage(),
                    'last_page'    => $paginatedStocks->lastPage(),
                ],
                'filters' => [
                    'brands'      => $availableBrands,
                    'price_range' => [
                        'min' => (float) ($priceRange->min_price ?? 0),
                        'max' => (float) ($priceRange->max_price ?? 0),
                    ],
                ],
                'success' => true,
                'status'  => 200,
            ]);
    }

    public function filter_variations(Request $request)
    {
        $stock = ProductStock::where('product_id', $request->product_id)->where('variant', $request->variant)->get();
        $variations = stock_price($stock);
        return response()->json(['success' => true, 'data' => $variations], 200);
    }

    public function filter_by_packqty(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'pack_qty'   => 'required',
                'size'       => 'nullable|string',
                'color'      => 'nullable|string',
                'flavour'    => 'nullable|string',
            ]);

            $baseQuery = ProductStock::where('product_id', $request->product_id)
                ->where('pack_qty', $request->pack_qty);

            // ── 1. Fetch ALL possible values (unfiltered lists) ─────────────────────
            $allSizes = (clone $baseQuery)
                ->whereNotNull('variant')
                ->where('variant', '!=', '')
                ->pluck('variant')
                ->unique()
                ->sort()
                ->values();

            $allColors = (clone $baseQuery)
                ->whereNotNull('color')
                ->where('color', '!=', '')
                ->pluck('color')
                ->unique()
                ->sort()
                ->values();

            $allFlavours = (clone $baseQuery)
                ->whereNotNull('flavour')
                ->where('flavour', '!=', '')
                ->pluck('flavour')
                ->unique()
                ->sort()
                ->values();

            // ── 2. Determine currently selected values (with fallback) ─────────────
            $selectedSize = $request->size && $allSizes->contains($request->size)
                ? $request->size
                : ($allSizes->isNotEmpty() ? $allSizes->first() : null);

            $selectedColor = $request->color && $allColors->contains($request->color)
                ? $request->color
                : ($allColors->isNotEmpty() ? $allColors->first() : null);

            $selectedFlavour = $request->flavour && $allFlavours->contains($request->flavour)
                ? $request->flavour
                : ($allFlavours->isNotEmpty() ? $allFlavours->first() : null);

            // ── 3. Get the currently matching stock variation(s) ──────────────────
            $variationQuery = clone $baseQuery;

            if ($selectedSize) {
                $variationQuery->where('variant', $selectedSize);
            }
            if ($selectedColor) {
                $variationQuery->where('color', $selectedColor);
            }
            if ($selectedFlavour) {
                $variationQuery->where('flavour', $selectedFlavour);
            }

            $currentVariation = $variationQuery->get();

            // ── 4. Get currently AVAILABLE options (filtered by other selections) ──
            // Available colors (filtered by selected size + flavour)
            $availableColorsQuery = clone $baseQuery;
            if ($selectedSize) {
                $availableColorsQuery->where('variant', $selectedSize);
            }
            if ($selectedFlavour) {
                $availableColorsQuery->where('flavour', $selectedFlavour);
            }
            $availableColors = $availableColorsQuery
                ->whereNotNull('color')
                ->where('color', '!=', '')
                ->pluck('color')
                ->unique()
                ->sort()
                ->values();

            // Available flavours (filtered by selected size + color)
            $availableFlavoursQuery = clone $baseQuery;
            if ($selectedSize) {
                $availableFlavoursQuery->where('variant', $selectedSize);
            }
            if ($selectedColor) {
                $availableFlavoursQuery->where('color', $selectedColor);
            }
            $availableFlavours = $availableFlavoursQuery
                ->whereNotNull('flavour')
                ->where('flavour', '!=', '')
                ->pluck('flavour')
                ->unique()
                ->sort()
                ->values();

            // ── 5. Prepare response ────────────────────────────────────────────────
            $responseData = [
                'sizes'            => $allSizes,                        // ← always full list
                'colors'           => $allColors->isNotEmpty() ? $availableColors : [],
                'flavours'         => $allFlavours->isNotEmpty() ? $availableFlavours : [],
                'selected_size'    => $selectedSize,
                'selected_color'   => $selectedColor,
                'selected_flavour' => $selectedFlavour,
                'variation'        => $currentVariation->isNotEmpty()
                    ? stock_price($currentVariation)
                    : [],
            ];

            // Tell frontend which variant types exist at all
            $variantTypes = [];
            if ($allSizes->isNotEmpty())    $variantTypes[] = 'size';
            if ($allColors->isNotEmpty())   $variantTypes[] = 'color';
            if ($allFlavours->isNotEmpty()) $variantTypes[] = 'flavour';
            if (empty($variantTypes))       $variantTypes = ['pack_qty_only'];

            return response()->json([
                'success'      => true,
                'variant_types' => $variantTypes,   // array e.g. ['size', 'color']
                'data'         => $responseData
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::critical('filter_by_packqty failed', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);
        }
    }






    public function variantPrice(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $str = '';
        $tax = 0;

        if ($request->has('color') && $request->color != "") {
            $str = Color::where('code', '#' . $request->color)->first()->name;
        }

        $var_str = str_replace(',', '-', $request->variants);
        $var_str = str_replace(' ', '', $var_str);

        if ($var_str != "") {
            $temp_str = $str == "" ? $var_str : '-' . $var_str;
            $str .= $temp_str;
        }
        return   $this->calc($product, $str, $request, $tax);

        /*
        $product_stock = $product->stocks->where('variant', $str)->first();
        $price = $product_stock->price;
        $stockQuantity = $product_stock->qty;


        //discount calculation
        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }
        $price += $tax;

        return response()->json([
            'product_id' => $product->id,
            'variant' => $str,
            'price' => (float)convert_price($price),
            'price_string' => format_price(convert_price($price)),
            'stock' => intval($stockQuantity),
            'image' => $product_stock->image == null ? "" : uploaded_asset($product_stock->image)
        ]);*/
    }

    // public function home()
    // {
    //     return new ProductCollection(Product::inRandomOrder()->physical()->take(50)->get());
    // }
}
