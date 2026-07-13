<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PosProductCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'id' => $data->id,
                    'stock_id' => $data->stock_id,
                    'product_code' => $data->product_code,
                    'name' => $data->name,
                    'thumbnail_image' => ($data->stock_image == null)  ? uploaded_asset($data->thumbnail_img) : uploaded_asset($data->stock_image),
                    'price' => pos_products_price($data->stock_id,$data->customer_detail_id),
                    'base_price' => pos_products_price($data->stock_id,$data->customer_detail_id),
                    'qty' => $data->stock_qty,
                    'variant' => $data->variant,
                    'digital' => $data->digital,
                    'unit_price' => $data->unit_price
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
