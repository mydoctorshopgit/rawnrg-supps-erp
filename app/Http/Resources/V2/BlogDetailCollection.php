<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogDetailCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'category'          => $this->category
                                    ? [
                                        'id'            => $this->category->id,
                                        'category_name' => $this->category->category_name,
                                        'slug'          => $this->category->slug,
                                        'created_at'    => optional($this->category->created_at)->toDateTimeString(),
                                      ]
                                    : null,
            'title'             => $this->title,
            'slug'              => $this->slug,
            'short_description' => $this->short_description,
            'description'       => $this->description,
            'banner'            => uploaded_asset($this->banner ?? $this->image),
            'meta_title'        => $this->meta_title,
            'meta_img'          => uploaded_asset($this->meta_img ?? $this->meta_image),
            'meta_description'  => $this->meta_description,
            'meta_keywords'     => $this->meta_keywords,
            'status'            => $this->status,
            'created_at'        => optional($this->created_at)->toDateTimeString(),
            'updated_at'        => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
