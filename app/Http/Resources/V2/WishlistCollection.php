<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WishlistCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'id' => (integer) $data->id,
                    'product' => [
                        'id' => $data->product->id,
                        'name' => $data->product->name,
                        'product_code' => $data->product->product_code,
                        'pip_code' => $data->product->pip_code,
                        // 'pip_codes' => $data->product->pip_code,
                        'slug' => $data->product->slug,
                        'price' => optional($data->product->stocks->first())->price ?? '',
                        'vat' => optional($data->product->taxes->first())->tax ?? '',
                        'thumbnail_image' => uploaded_asset($data->product->thumbnail_img),
                        'base_price' => format_price(home_base_price($data->product, false)) ,
                        'rating' => (double) $data->product->rating,
                    ]
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
