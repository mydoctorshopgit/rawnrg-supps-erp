<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchProductResource extends JsonResource
{
    public function toArray($request)
    {
        $data = $this;

        $productCode = (!empty($data->sku) && $data->sku != 0)
            ? $data->sku
            : $data->pip_code;

        $productCode = str_replace('/', '-', trim((string) $productCode));

        $slugParts = [$data->product?->slug];

        if (!empty($data->flavour)) {
            $slugParts[] = strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', trim($data->flavour)));
        } elseif (!empty($data->variant) && $data->variant != 0) {
            $slugParts[] = strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', trim($data->variant)));
            if (!empty($data->color)) {
                $slugParts[] = strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', trim($data->color)));
            }
        }

        if (!empty($productCode)) {
            $slugParts[] = $productCode;
        }

        return [
            'id' => $data->id,
            'product_id' => $data->product_id,
            'variant' => $data->variant,
            'sku' => $data->sku,
            'pip_code' => $data->pip_code,
            'slug' => implode('-', array_filter($slugParts)),
            'order' => $data->order,
            'price' => $data->price,
            'total_price' => toFixedDown(
                $data->price +
                    round(
                        $data->price *
                            (optional($data->product?->taxes->first())->tax ?? 0) / 100,
                        2
                    ),
                2
            ),
            'qty' => $data->qty,
            'pack_qty' => $data->pack_qty,
            'image' => uploaded_asset($data->image),
            'description' => $data->description,
            'short_description' => $data->short_description,

            'photos' => collect(json_decode($data->photos, true) ?? explode(',', $data->photos))
                ->filter()
                ->map(fn ($photo) => uploaded_asset(trim($photo)))
                ->values(),

            'thumbnail_image' => uploaded_asset($data->thumbnail_img),
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,

            'product' => [
                'id' => $data->product?->id,
                'name' => $data->product?->name,
                'brand' => optional($data->product?->brand)->name ?? '',
                'category' => optional($data->product?->main_category)->id,
                'product_code' => $data->product?->product_code,
                'vat' => round(
                    (optional($data->product?->taxes->first())->tax / 100) *
                    (optional($data->product?->stocks->first())->price ?? 0),
                    2
                ),
                'canonical_slug' => $data->product?->slug,
                'unit_price' => $data->product?->unit_price,
                'description' => $data->product?->description,
                'short_description' => $data->product?->short_description,
                'thumbnail' => $data->product?->thumbnail
                    ? asset($data->product->thumbnail->file_name)
                    : null,
                'shipping_type' => $data->product?->shipping_type,
                'shipping_cost' => $data->product?->shipping_cost,
                'est_shipping_days' => $data->product?->est_shipping_days,
                'rating' => $data->product?->rating,
                'pack_qty' => $data->product?->pack_qty,
                'current_stock' => $data->product?->current_stock,
                'unit' => $data->product?->unit,
                'cash_on_delivery' => $data->product?->cash_on_delivery,
                'is_wishlist' => auth()->check()
                    ? $data->product?->wishlists()->where('user_id', auth()->id())->exists()
                    : false,
                'refundable' => $data->product?->refundable,
            ],
        ];
    }
}
