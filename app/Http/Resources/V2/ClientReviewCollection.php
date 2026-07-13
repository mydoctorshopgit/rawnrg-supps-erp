<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClientReviewCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($review) {
            return [
                'id'         => $review->id,
                'name'       => $review->name,
                'role'       => $review->role,
                'image'      => uploaded_asset($review->image),
                'rating'     => (float) $review->rating,
                'comment'    => $review->comment,
                'created_at' => optional($review->created_at)->toDateTimeString(),
            ];
        })->values();
    }
}
