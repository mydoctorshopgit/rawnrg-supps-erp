<?php

namespace App\Http\Resources\V2;

use App\Http\Resources\Concerns\BuildsProductSlug;
use Illuminate\Http\Resources\Json\JsonResource;

class BestSellerCategoryCollection extends JsonResource
{
    use BuildsProductSlug;
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,

            // ✅ SAFE
            'products' => $this->formatProducts(
                $this->whenLoaded('bestSellerProducts')
            ),
        ];
    }

    private function formatProducts($products)
    {
        // ✅ Ensure always a collection
        $products = collect($products);

        return $products->map(function ($product) {
            $lowestStock = $product->stocks->sortBy('price')->first();
            $price       = (float) ($lowestStock->price ?? 0);
            $taxRate     = (optional($product->taxes->first())->tax ?? 0) / 100;
            $vat         = round($taxRate * $price, 2);

            return [
                'id'              => $product->id,
                'name'            => $product->name,
                'thumbnail_image' => uploaded_asset($product->thumbnail_img, 'small'),
                'has_discount'    => home_base_price($product, false) != home_discounted_base_price($product, false),
                'discount'        => '-' . discount_in_percentage($product) . '%',
                'canonical_slug'  => $product->slug,
                'slug'            => $lowestStock ? $this->buildFullSlug($lowestStock, $product->slug) : $product->slug,
                'size_slug'       => $lowestStock ? $this->buildSizeSlug($lowestStock, $product->slug) : $product->slug,
                'rating'          => (float) $product->rating,
                'product_code'    => $product->product_code,
                'pip_code'        => $product->pip_code,
                'vat'             => $vat,
                'price'           => $price,
                'total_price'     => round($price + $vat, 2),
                'is_wishlist'     => ($uid = auth('sanctum')->id()) ? $product->wishlists()->where('user_id', $uid)->exists() : false,
            ];
        })->values(); // reset keys
    }
}