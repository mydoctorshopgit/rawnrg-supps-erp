<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoriesCollection extends ResourceCollection
{
    protected $products;

    public function __construct($resource, $products)
    {
        parent::__construct($resource);
        $this->products = $products;
    }

    public function toArray($request)
    {
        return [
            'parent_categories' => $this->formatCategories($this->collection),
            'best_seller_products' => $this->formatProducts($this->products)
        ];
    }

    private function formatCategories($categories)
    {
        return $categories->map(function ($category) {
            return [
                'id'   => $category->id,
                'name' => $category->name,
                'parent' => $category->parent_id,
                'child_categories' => $this->formatCategories($category->categories)
            ];
        });
    }

    private function formatProducts($products)
    {
        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'best_seller' => $product->best_seller,
                'price' => $product->unit_price ?? 0
            ];
        });
    }
}