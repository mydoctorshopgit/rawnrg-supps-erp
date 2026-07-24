<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class TopPickCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'banner' => uploaded_asset($this->banner) ?? '',
            'icon'  => uploaded_asset($this->icon) ?? '',
            'banner_alt' => $this->banner_alt,
            'icon_alt' => $this->icon_alt,
            'cover_image_alt' => $this->cover_image_alt,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'cover_image' => uploaded_asset($this->cover_image) ?? '',
            'color' => $this->color ?? '',
            'lite_color' => $this->lite_color ?? '',
            'tagline' => $this->tagline ?? '',
            'short_description' => $this->short_description ?? '',
            'products' => new ProductCollection($this->whenLoaded('topPickProducts')),
        ];
    }
}
