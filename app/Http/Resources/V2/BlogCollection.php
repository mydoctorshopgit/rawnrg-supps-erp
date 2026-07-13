<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BlogCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'success' => true,
            'data' => $this->collection->map(function ($blog) {
                $cat = $blog->category; // already resolved via belongsTo — do NOT re-query

                return [
                    'id'                => $blog->id,
                    'category'          => $cat ? [
                                            'id'            => $cat->id,
                                            'category_name' => $cat->category_name,
                                            'slug'          => $cat->slug,
                                            'created_at'    => optional($cat->created_at)->toDateTimeString(),
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
                    'updated_at'        => optional($blog->updated_at)->toDateTimeString(),
                ];
            })
        ];
    }
}