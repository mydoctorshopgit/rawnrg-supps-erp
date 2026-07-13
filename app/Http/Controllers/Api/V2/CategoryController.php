<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\CategoriesCollection;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;

class CategoryController extends Controller
{

    //    public function categories(Request $request)
    //     {
    //         $parentCategories = Category::with(['categories.categories'])
    //             ->where('parent_id', 0)
    //             ->get();
    //          $product =  Product::where('category_id' , $request->category_id)->where('best_seller' , 1)->get();   

    //         return new CategoriesCollection($parentCategories,$product);
    //     }

    public function categories(Request $request)
    {
        $categoryId = $request->category_id;
        $parentCategories = Cache::remember('categories_tree', 600, function () {
            return DB::table('categories as parent')
                ->select('parent.id', 'parent.name')
                ->where('parent.parent_id', 0)
                ->get()
                ->map(function ($parent) {

                    $children = DB::table('categories')
                        ->select('id', 'name', 'parent_id')
                        ->where('parent_id', $parent->id)
                        ->get()
                        ->map(function ($child) {

                            $sub = DB::table('categories')
                                ->select('id', 'name', 'parent_id')
                                ->where('parent_id', $child->id)
                                ->get();

                            $child->sub_categories = $sub;
                            return $child;
                        });

                    $parent->child_categories = $children;
                    return $parent;
                });
        });
        $products = Cache::remember("best_seller_{$categoryId}", 300, function () use ($categoryId) {
            // Use a UNION so we catch products assigned to this category either via
            // the primary category_id column OR via the product_categories pivot table.
            $byPrimary = DB::table('products')
                ->select('id', 'name', 'thumbnail_img', 'unit_price')
                ->where('category_id', $categoryId)
                ->where('best_seller', 1);

            return DB::table('products')
                ->select('products.id', 'products.name', 'products.thumbnail_img', 'products.unit_price')
                ->join('product_categories', 'product_categories.product_id', '=', 'products.id')
                ->where('product_categories.category_id', $categoryId)
                ->where('products.best_seller', 1)
                ->union($byPrimary)
                ->limit(10)
                ->get();
        });

        return response()->json([
            'success' => true,
            'categories' => $parentCategories,
            'best_seller_products' => $products,
            'response_time' => round(microtime(true) - LARAVEL_START, 3) . 's'
        ]);
    }



    // public function categories(Request $request)
    // {
    //     $parent_category = Category::where('parent_id', 0)->get();

    //     $categories = Category::with('categories.categories')
    //         ->where('parent_id', 0)
    //         ->get();

    // //     $product_id = $request->product_id;
    // //     $product_categories = null;

    // //     if ($product_id) {
    // //     $product = Product::where('id', $product_id)
    // //             ->with(['categories' => function($query) {
    // //                 $query->where('best_seller', 1); 
    // //             }])
    // //             ->first();

    // //         if ($product) {
    // //             $product_categories = $product->categories;
    // //         }
    // //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' =>  new CategoriesCollection($parent_category,$categories),
    //     ]);
    // }
    //     public function index($parent_id = 0)
    //     {
    //         if(request()->has('parent_id') && is_numeric (request()->get('parent_id'))){
    //           $parent_id = request()->get('parent_id');
    //         }

    //             return new CategoryCollection(Category::where('parent_id', $parent_id)->whereDigital(0)->get());
    //     }

    //     public function featured()
    //     {
    //             return new CategoryCollection(Category::where('featured', 1)->get());
    //     }

    //     public function home()
    //     {
    //             return new CategoryCollection(Category::whereIn('id', json_decode(get_setting('home_categories')))->get());
    //     }

    //     public function top()
    //     {   
    //             return new CategoryCollection(Category::whereIn('id', json_decode(get_setting('home_categories')))->limit(20)->get());
    //     }
}
