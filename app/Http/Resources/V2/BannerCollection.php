<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BannerCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'count' => $this->collection->count(),

            'data' => $this->collection->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'description' => $banner->description,
                    'image'  => uploaded_asset($banner->image),
                    'created_at' => $banner->created_at?->toDateTimeString(),
                ];
            })
        ];
    }
}
