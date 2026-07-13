<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $mapped = $this->collection->map(function ($data) {
            return [
                'id'          => $data->id,
                'name'        => $data->getTranslation('name'),
                'logo'        => uploaded_asset($data->logo),
                'is_featured' => (bool) $data->featured,
                'order_level' => (int) $data->order_level,
                'links'       => [
                    'products' => route('api.products.brand', $data->id),
                ],
            ];
        });

        $result = ['data' => $mapped];

        // Append pagination metadata when the underlying resource is a paginator
        if ($this->resource instanceof LengthAwarePaginator) {
            $result['pagination'] = [
                'total'        => $this->resource->total(),
                'per_page'     => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page'    => $this->resource->lastPage(),
                'from'         => $this->resource->firstItem(),
                'to'           => $this->resource->lastItem(),
                'next_page_url' => $this->resource->nextPageUrl(),
                'prev_page_url' => $this->resource->previousPageUrl(),
            ];
        }

        return $result;
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status'  => 200,
        ];
    }
}
