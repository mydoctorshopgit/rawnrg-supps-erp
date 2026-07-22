<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BlogCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($blog) {
                return [
                    'id'                => $blog->id,
                    'category'          => $blog?->category ? [
                                                'id'            => $blog->category->id,
                                                'category_name' => $blog->category->category_name,
                                                'slug'          => $blog->category->slug,
                                            ] : null,
                    'title'             => $blog->title,
                    'slug'              => $blog->slug,
                    'short_description' => $blog->short_description,
                    'description'       => $blog->description,
                    'banner'            => uploaded_asset($blog->banner ?? $blog->image),
                    'meta_title'        => $blog->meta_title,
                    'meta_img'          => uploaded_asset($blog->meta_img ?? $blog->meta_image),
                    'meta_description'  => $blog->meta_description,
                    'meta_keywords'     => $blog->meta_keywords,
                    'status'            => $blog->status,
                    'created_at'        => optional($blog->created_at)->toDateTimeString(),
                ];
            })
        ];
    }
}
