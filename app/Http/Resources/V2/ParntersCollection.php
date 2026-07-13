<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ParntersCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'success' => true,
            'count' => $this->collection->count(),

            'data' => $this->collection->map(function ($partner) {
                return [
                    'id'     => $partner->id,
                    'name'  => $partner->name,
                    'image'  => uploaded_asset($partner->image),
                    'status' => $partner->status,
                    'created_at' => optional($partner->created_at)->toDateTimeString(),
                ];
            })
        ];
    }
}