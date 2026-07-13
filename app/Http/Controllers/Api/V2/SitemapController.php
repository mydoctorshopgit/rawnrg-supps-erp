<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::pluck('slug')->all();
        $categories = Category::where('status', 1)->get()->map(function ($category) {
            return $category->full_slug;
        });

        return response()->json(['products' => $products, 'categories' => $categories, 'success' => true]);
    }
}
