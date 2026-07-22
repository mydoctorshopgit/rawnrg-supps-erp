<?php

namespace App\Http\Resources\V2;

use App\Http\Resources\Concerns\BuildsProductSlug;
use App\Models\ProductStock;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductSingleCollection extends ResourceCollection
{
    use BuildsProductSlug;
    protected $requestedSlug;

    public function __construct($resource, $requestedSlug = null)
    {
        parent::__construct($resource);
        $this->requestedSlug = $requestedSlug;
    }

    public function toArray($request)
    {
        $requestedSlug = $this->requestedSlug;

        return [
            'data' => $this->collection->map(function ($data) use ($requestedSlug) {

                // Use the lowest-price stock
                $lowestStock = $data->stocks->sortBy('price')->first();
                $price    = (float) ($lowestStock->price ?? 0);

                $discount = 0;
                if($data->discount_type !== null && $data->discount !== null) {
                    if($data->discount_type === 'amount') {
                        $discount = $data->discount;
                    } else {
                        $discount = ($price * $data->discount) / 100;
                    }
                }
                
                $discount_price = $price - $discount;

                $taxRate  = (optional($data->taxes->first())->tax ?? 0) / 100;
                $vat      = round($taxRate * $discount_price, 2);
                $totalPrice = round($discount_price + $vat, 2);

                return [
                    'id'             => $data->id,
                    'name'           => $data->name,
                    'thumbnail_image'=> uploaded_asset($data->thumbnail_img, 'small'),
                    'canonical_slug' => $data->slug,
                    'slug'           => $lowestStock ? $this->buildFullSlug($lowestStock, $data->slug) : $data->slug,
                    'size_slug'      => $lowestStock ? $this->buildSizeSlug($lowestStock, $data->slug) : $data->slug,
                    'has_discount'   => home_base_price($data, false) != home_discounted_base_price($data, false),
                    'discount'       => '-' . discount_in_percentage($data) . '%',
                    'rating'         => (float) $data->rating,
                    'product_code'   => $lowestStock->sku ?? $data->product_code,
                    'pip_code'       => $lowestStock->pip_code ?? $data->pip_code,
                    'net_price'       => single_price($price),
                    'vat'            => single_price($vat),
                    'price'          => single_price($discount_price),
                    'total_price'    => single_price($totalPrice),
                    // Feature flags
                    'is_top_rated'   => (bool) $data->todays_deal,
                    'is_featured'    => (bool) $data->featured,
                    // 'is_monthly_deal'=> (bool) $data->monthly_deal,
                    'is_best_seller' => (bool) $data->best_seller,
                    'is_trending'    => (bool) $data->is_trending,
                    'is_save_big'    => (bool) $data->save_big,
                    'is_wishlist'    => ($uid = auth('sanctum')->id()) ? $data->wishlists()->where('user_id', $uid)->exists() : false,
                    'color'      => $data->main_category->color ?? null,
                    'lite_color' => $data->main_category->lite_color ?? null,
                    
                ];
            }),
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200,
        ];
    }
}
