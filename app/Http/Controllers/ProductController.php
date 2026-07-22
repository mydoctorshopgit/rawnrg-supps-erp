<?php

namespace App\Http\Controllers;

use Str;
use Cache;
use Artisan;
use Combinations;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use App\Models\ProductTax;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\AttributeValue;
// use CoreComponentRepository;
use App\Models\ProductCategory;
use App\Services\ProductService;
use App\Models\ProductTranslation;
use App\Services\ProductTaxService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use App\Http\Requests\ProductRequest;
use App\Services\ProductStockService;
use Illuminate\Support\Facades\Redirect;
use App\Services\ProductFlashDealService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ShopProductNotification;
use AizPackages\CombinationGenerate\Services\CombinationService;

class ProductController extends Controller
{
    protected $productService;
    protected $productTaxService;
    protected $productFlashDealService;
    protected $productStockService;

    public function __construct(
        ProductService $productService,
        ProductTaxService $productTaxService,
        ProductFlashDealService $productFlashDealService,
        ProductStockService $productStockService
    ) {
        $this->productService = $productService;
        $this->productTaxService = $productTaxService;
        $this->productFlashDealService = $productFlashDealService;
        $this->productStockService = $productStockService;

        // Staff Permission Check
        $this->middleware(['permission:add_new_product'])->only('create');
        $this->middleware(['permission:show_all_products'])->only('all_products');
        $this->middleware(['permission:show_in_house_products'])->only('admin_products');
        $this->middleware(['permission:show_seller_products'])->only('seller_products');
        $this->middleware(['permission:product_edit'])->only('admin_product_edit', 'seller_product_edit');
        $this->middleware(['permission:product_duplicate'])->only('duplicate');
        $this->middleware(['permission:product_delete'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_products(Request $request)
    {
        $type = 'In House';
        $col_name = null;
        $query = null;
        $sort_search = null;

        $products = Product::where('added_by', 'admin')
            ->where('auction_product', 0)
            ->where('wholesale_product', 0)
            ->where('digital', 0);

        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
        }

        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products->where(function ($q) use ($sort_search) {
                $q->where('name', 'like', '%' . $sort_search . '%')
                  ->orWhereHas('stocks', fn($sq) => $sq->where('sku', 'like', '%' . $sort_search . '%'));
            });
        }

        $products = $products
            ->with(['main_category:id,name', 'stocks:id,product_id,variant,qty'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'sort_search'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function seller_products(Request $request, $product_type)
    {
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;

        $products = Product::where('added_by', 'seller')
            ->where('auction_product', 0)
            ->where('wholesale_product', 0);

        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products->where('name', 'like', '%' . $sort_search . '%');
        }
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
        }

        $products = $product_type == 'physical'
            ? $products->where('digital', 0)
            : $products->where('digital', 1);

        $products = $products
            ->with(['main_category:id,name', 'stocks:id,product_id,variant,qty'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $type = 'Seller';

        // Pre-load sellers for the dropdown (avoid raw query in blade)
        $sellers = \App\Models\User::where('user_type', 'seller')
            ->select('id', 'name')
            ->with('shop:user_id,name')
            ->get();

        if ($product_type == 'digital') {
            return view('backend.product.digital_products.index', compact('products', 'sort_search', 'type'));
        }
        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search', 'sellers'));
    }
    public function product_seller_status(Request $request)
    {
        $product = Product::findOrFail($request->id);

        if ($request->status == 1) {

            $count = Product::where('category_id', $product->category_id)
                ->where('best_seller', 1)
                ->count();

            if ($count >= 8) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Only 8 best seller products allowed in same category'
                ]);
            }
        }

        $product->best_seller = $request->status;
        $product->save();

        // Clear best seller cache for all categories this product belongs to
        // (both primary category_id and any pivot-table categories)
        $categoryIds = $product->categories()->pluck('categories.id')->toArray();
        if ($product->category_id && !in_array($product->category_id, $categoryIds)) {
            $categoryIds[] = $product->category_id;
        }
        foreach ($categoryIds as $catId) {
            Cache::forget("best_seller_{$catId}");
        }

        return response()->json([
            'status' => 1,
            'message' => 'Updated successfully'
        ]);
    }

    public function all_products(Request $request)
    {
        $categories = Category::select('id', 'name', 'best_seller')->orderBy('name')->get();
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;

        $products = Product::where('auction_product', 0)->where('wholesale_product', 0);

        if (get_setting('vendor_system_activation') != 1) {
            $products = $products->where('added_by', 'admin');
        }
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->category_id != null) {
            $products = $products->where('category_id', $request->category_id);
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products->where(function ($q) use ($sort_search) {
                $q->where('name', 'like', '%' . $sort_search . '%')
                  ->orWhereHas('stocks', fn($sq) => $sq->where('sku', 'like', '%' . $sort_search . '%'));
            });
        }
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
        }

        $products = $products
            ->with(['main_category:id,name', 'stocks:id,product_id,variant,qty'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $type = 'All';

        // Pre-load sellers for the dropdown (avoid raw query in blade)
        $sellers = \App\Models\User::whereIn('user_type', ['admin', 'seller'])
            ->select('id', 'name')
            ->get();

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search', 'categories', 'sellers'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // CoreComponentRepository::initializeCache();



        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.products.create', compact('categories'));
    }

    public function add_more_choice_option(Request $request)
    {
        $all_attribute_values = AttributeValue::with('attribute')->where('attribute_id', $request->attribute_id)->get();

        $html = '';

        foreach ($all_attribute_values as $row) {
            $html .= '<option value="' . $row->value . '">' . $row->value . '</option>';
        }

        echo json_encode($html);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        // dd($request);
        // return $request;
        $product = $this->productService->store($request->except([
            '_token',
            'sku',
            'choice',
            'tax_id',
            'tax',
            'tax_type',
            'flash_deal_id',
            'flash_discount',
            'flash_discount_type',
            'gallery_alt',
            'thumbnail_alt'
        ]));
        $request->merge(['product_id' => $product->id]);


        $product->thumbnail_alt = !empty($request->thumbnail_alt) ? json_encode($request->thumbnail_alt) : json_encode([]);
        $product->gallery_alt = !empty($request->gallery_alt) ? json_encode($request->gallery_alt) : json_encode([]);

        $product->category_id = $request->category_ids[0] ?? null;

        // Build FAQ JSON from repeater fields
        if ($request->has('faq_questions')) {
            $faqs = [];
            foreach ($request->faq_questions as $i => $question) {
                $answer = $request->faq_answers[$i] ?? '';
                if (!empty(trim($question))) {
                    $faqs[] = ['question' => trim($question), 'answer' => trim($answer)];
                }
            }
            $product->faq = !empty($faqs) ? json_encode($faqs) : null;
        }

        $product->save();

        //Product categories
        $product->categories()->attach($request->category_ids);

        //VAT & Tax
        if ($request->tax_id) {
            $this->productTaxService->store($request->only([
                'tax_id',
                'tax',
                'tax_type',
                'product_id'
            ]));
        }

        //Flash Deal
        $this->productFlashDealService->store($request->only([
            'flash_deal_id',
            'flash_discount',
            'flash_discount_type'
        ]), $product);

        //Product Stock
        // $this->productStockService->store($request->only([
        //     'colors_active',
        //     'colors',
        //     'choice_no',
        //     'unit_price',
        //     'sku',
        //     'current_stock',
        //     'product_id'
        // ]), $product);

        if ($request->has('stocks')) {
            foreach ($request->stocks as $stockData) {
                // Check if stock is marked for deletion
                if (isset($stockData['deleted']) && $stockData['deleted'] == '1') {
                    if (isset($stockData['id']) && $stockData['id']) {
                        ProductStock::where('id', $stockData['id'])->delete();
                    }
                    continue;
                }

                // Update or Create Stock
                $stock = isset($stockData['id']) && $stockData['id']
                    ? ProductStock::find($stockData['id'])
                    : new ProductStock();

                if ($stock) {
                    $stock->product_id = $product->id;
                    $stock->photos = $stockData['photos'] ?? null;
                    $stock->thumbnail_img = $stockData['thumbnail_img'] ?? null;
                    $stock->thumbnail_alt = !empty($stockData['thumbnail_alt']) ? json_encode($stockData['thumbnail_alt']) : json_encode([]);
                    $stock->gallery_alt = !empty($stockData['gallery_alt']) ? json_encode($stockData['gallery_alt']) : json_encode([]);
                    $stock->pip_code = $stockData['pip_code'] ?? null;
                    $stock->sku = $stockData['sku'] ?? null;
                    $stock->variant = $stockData['variant'] ?? null;
                    $stock->flavour = $stockData['flavour'] ?? null;
                    $stock->color = $stockData['color'] ?? null;
                    $stock->qty = $stockData['qty'] ?? null;
                    $stock->pack_qty = $stockData['pack_qty'] ?? null;
                    $stock->price = $stockData['price'] ?? null;
                    $stock->description = $stockData['description'] ?? null;
                    $stock->short_description = $stockData['short_description'] ?? null;
                    $stock->features = $stockData['features'] ?? null;
                    $stock->save();
                }
            }
        }

        // Product Translations
        $request->merge(['lang' => env('DEFAULT_LANGUAGE')]);
        ProductTranslation::create($request->only([
            'lang',
            'name',
            'unit',
            'description',
            'product_id'
        ]));

        flash(translate('Product has been inserted successfully'))->success();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return redirect()->route('products.all');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function admin_product_edit(Request $request, $id)
    {
        // CoreComponentRepository::initializeCache();

        $product = Product::findOrFail($id);
        $product_stocks = ProductStock::where('product_id', $id)->get();
        if ($product->digital == 1) {
            return redirect('admin/digitalproducts/' . $id . '/edit');
        }

        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        return view('backend.product.products.edit', compact('product', 'product_stocks', 'categories', 'tags', 'lang'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function seller_product_edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        if ($product->digital == 1) {
            return redirect('digitalproducts/' . $id . '/edit');
        }
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        // $categories = Category::all();
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        // Map flash_discount fields to the actual product columns
        if ($request->has('flash_discount')) {
            $request->merge([
                'discount'      => $request->flash_discount,
                'discount_type' => $request->flash_discount_type,
            ]);
        }

        $product = $this->productService->update(
            $request->except([
                '_token',
                'sku',
                'choice',
                'tax_id',
                'tax',
                'tax_type',
                'flash_deal_id',
                'flash_discount',
                'flash_discount_type',
                'gallery_alt',
                'thumbnail_alt'
            ]),
            $product
        );

        $request->merge(['product_id' => $product->id]);

        $product->thumbnail_alt = !empty($request->thumbnail_alt) ? json_encode($request->thumbnail_alt) : json_encode([]);
        $product->gallery_alt = !empty($request->gallery_alt) ? json_encode($request->gallery_alt) : json_encode([]);

        $product->category_id = $request->category_ids[0] ?? null;
        $product->edit_by = Auth::user()?->name;

        // Build FAQ JSON from repeater fields
        if ($request->has('faq_questions')) {
            $faqs = [];
            foreach ($request->faq_questions as $i => $question) {
                $answer = $request->faq_answers[$i] ?? '';
                if (!empty(trim($question))) {
                    $faqs[] = ['question' => trim($question), 'answer' => trim($answer)];
                }
            }
            $product->faq = !empty($faqs) ? json_encode($faqs) : null;
        }

        $product->save();

        $product->categories()->sync($request->category_ids ?? []);


        if ($request->tax_id) {
            $product->taxes()->delete();
            $this->productTaxService->store($request->only([
                'tax_id',
                'tax',
                'tax_type',
                'product_id'
            ]));
        }

        // ------------------------------
        // Handle Product Stocks
        // ------------------------------
        if ($request->filled('stocks')) {

            // Snapshot existing IDs before we touch anything
            $existingIds = ProductStock::where('product_id', $product->id)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->toArray();

            $processedIds = []; // IDs we actually saved/updated this request

            foreach ($request->stocks as $stockData) {

                $stockId  = !empty($stockData['id']) ? (int) $stockData['id'] : null;
                $isDelete = !empty($stockData['deleted']) && $stockData['deleted'] == '1';

                // ── Delete: only if it's an existing DB record ────────────────
                if ($isDelete) {
                    if ($stockId && in_array($stockId, $existingIds)) {
                        ProductStock::where('id', $stockId)->delete();
                    }
                    continue; // never add to processedIds
                }

                // ── Update existing ───────────────────────────────────────────
                if ($stockId && in_array($stockId, $existingIds)) {
                    $stock = ProductStock::find($stockId);
                    if (!$stock) continue; // race condition guard
                    $processedIds[] = $stockId;

                // ── Insert new ────────────────────────────────────────────────
                } else {
                    $stock = new ProductStock();
                }

                $stock->product_id        = $product->id;
                $stock->thumbnail_img     = $stockData['thumbnail_img'] ?? null;
                $stock->photos            = $stockData['photos'] ?? null;
                $stock->thumbnail_alt     = !empty($stockData['thumbnail_alt'])
                                            ? json_encode($stockData['thumbnail_alt'])
                                            : json_encode([]);
                $stock->gallery_alt       = !empty($stockData['gallery_alt'])
                                            ? json_encode($stockData['gallery_alt'])
                                            : json_encode([]);
                $stock->pip_code          = $stockData['pip_code'] ?? null;
                $stock->sku               = $stockData['sku'] ?? null;
                $stock->variant           = $stockData['variant'] ?? null;
                $stock->flavour           = $stockData['flavour'] ?? null;
                $stock->color             = $stockData['color'] ?? null;
                $stock->qty               = $stockData['qty'] ?? null;
                $stock->pack_qty          = $stockData['pack_qty'] ?? null;
                $stock->price             = $stockData['price'] ?? null;
                $stock->description       = $stockData['description'] ?? null;
                $stock->short_description = $stockData['short_description'] ?? null;
                $stock->features          = $stockData['features'] ?? null;
                $stock->save();
            }

            // ── Orphan cleanup: existing stocks not touched and not deleted ───
            // These are stocks that were in DB but never appeared in the request
            // (e.g. form was submitted without them — shouldn't happen normally,
            //  but guards against stale data)
            $orphans = array_diff($existingIds, $processedIds);
            // NOTE: We do NOT auto-delete orphans here — the deleted flag is the
            // explicit contract. Orphans only happen on a broken form submission.
            // Uncomment below only if you want strict sync behaviour:
            // if (!empty($orphans)) ProductStock::whereIn('id', $orphans)->delete();
        }

        // ------------------------------
        // Success and cache clearing
        // ------------------------------
        flash(translate('Product has been updated successfully'))->success();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        if ($request->filled('tab')) {
            return redirect()->to(url()->previous() . '#' . $request->tab);
        }

        return back();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->product_translations()->delete();
        $product->categories()->detach();
        $product->stocks()->delete();
        $product->taxes()->delete();

        if (Product::destroy($id)) {
            Cart::where('product_id', $id)->delete();
            Wishlist::where('product_id', $id)->delete();

            flash(translate('Product has been deleted successfully'))->success();

            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            return back();
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function bulk_product_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $product_id) {
                $this->destroy($product_id);
            }
        }

        return 1;
    }

    /**
     * Duplicates the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Request $request, $id)
    {
        $product = Product::find($id);

        //Product
        $product_new = $this->productService->product_duplicate_store($product);

        //Product Stock
        $this->productStockService->product_duplicate_store($product->stocks, $product_new);

        //VAT & Tax
        $this->productTaxService->product_duplicate_store($product->taxes, $product_new);

        flash(translate('Product has been duplicated successfully'))->success();
        if ($request->type == 'In House')
            return redirect()->route('products.admin');
        elseif ($request->type == 'Seller')
            return redirect()->route('products.seller');
        elseif ($request->type == 'All')
            return redirect()->route('products.all');
    }

    public function get_products_by_brand(Request $request)
    {
        $products = Product::where('brand_id', $request->brand_id)->get();
        return view('partials.product_select', compact('products'));
    }

    public function updateTodaysDeal(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->todays_deal = $request->status;
        $product->save();
        Cache::forget('todays_deal_products');
        return 1;
    }

    public function updatePublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;

        if ($product->added_by == 'seller' && addon_is_activated('seller_subscription') && $request->status == 1) {
            $shop = $product->user->shop;
            if (
                $shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($shop->package_invalid_at), false) < 0
                || $shop->product_upload_limit <= $shop->user->products()->where('published', 1)->count()
            ) {
                return 0;
            }
        }

        $product->save();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        return 1;
    }

    public function updateProductApproval(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->approved = $request->approved;

        if ($product->added_by == 'seller' && addon_is_activated('seller_subscription')) {
            $shop = $product->user->shop;
            if (
                $shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($shop->package_invalid_at), false) < 0
                || $shop->product_upload_limit <= $shop->user->products()->where('published', 1)->count()
            ) {
                return 0;
            }
        }

        $product->save();

        $product_type   = $product->digital ==  0 ? 'physical' : 'digital';
        $status         = $request->approved == 1 ? 'approved' : 'rejected';
        $users          = User::findMany([User::where('user_type', 'admin')->first()->id, $product->user_id]);
        Notification::send($users, new ShopProductNotification($product_type, $product, $status));

        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        return 1;
    }

    public function updateFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->featured = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return response()->json(1);
        }
        return response()->json(0);
    }

    public function updateTrending(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->is_trending = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return response()->json(1);
        }
        return response()->json(0);
    }

    public function update_monthly_deals(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->monthly_deal = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return response()->json(1);
        }
        return response()->json(0);
    }

    public function updateSaveBig(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->save_big = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return response()->json(1);
        }
        return response()->json(0);
    }
    public function updatePharma(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->pharmaceutical_product = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
    }

    public function sku_combination(Request $request)
    {
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                // foreach (json_decode($request[$name][0]) as $key => $item) {
                if (isset($request[$name])) {
                    $data = array();
                    foreach ($request[$name] as $key => $item) {
                        // array_push($data, $item->value);
                        array_push($data, $item);
                    }
                    array_push($options, $data);
                }
            }
        }

        $combinations = (new CombinationService())->generate_combination($options);
        return view('backend.product.products.sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'));
    }

    public function sku_combination_edit(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $product_name = $request->name;
        $unit_price = $request->unit_price;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                // foreach (json_decode($request[$name][0]) as $key => $item) {
                if (isset($request[$name])) {
                    $data = array();
                    foreach ($request[$name] as $key => $item) {
                        // array_push($data, $item->value);
                        array_push($data, $item);
                    }
                    array_push($options, $data);
                }
            }
        }

        $combinations = (new CombinationService())->generate_combination($options);
        return view('backend.product.products.sku_combinations_edit', compact('combinations', 'unit_price', 'colors_active', 'product_name', 'product'));
    }


    // aiz file upload
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads/editor', 'public');
            return response()->json(['url' => asset('storage/' . $path)]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function updateTopPick(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->is_top_pick = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return response()->json(1);
        }
        return response()->json(0);
    }
}
