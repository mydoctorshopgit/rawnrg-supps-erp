<?php

namespace App\Http\Resources\V2;

use App\Models\ProductStock;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductMiniCollection extends ResourceCollection
{
    protected $requestedSlug;

    public function __construct($resource, $requestedSlug = null)
    {
        parent::__construct($resource);
        $this->requestedSlug = $requestedSlug;
    }

    public function toArray($request)
    {
        $requestedSlug = $this->requestedSlug;

        return [
            'data' => $this->collection->map(function ($data) use ($requestedSlug) {
                $wholesale_product = ($data->wholesale_product == 1);

                $price = optional($data->stocks->first())->price ?? 0;
                $vat = round(((optional($data->taxes->first())->tax ?? 0) / 100) * $price, 2);
                $finalPrice = $price + $vat;

                $images = collect(json_decode($data->photos, true) ?? explode(',', $data->photos))
                    ->filter()
                    ->map(fn($photo) => uploaded_asset(trim($photo)))
                    ->toArray();

                $schema = [
                    "@context" => "https://schema.org/",
                    "@type" => "Product",
                    "name" => $data->meta_title ?? $data->getTranslation('name'),
                    "image" => array_merge([uploaded_asset($data->thumbnail_img)], $images),
                    "description" => strip_tags($data->short_description ?: $data->description),
                    "sku" => $data->pip_code ?: $data->product_code,
                    "mpn" => (string) $data->id,
                    "brand" => [
                        "@type" => "Brand",
                        "name" => optional($data->brand)->name ?? env('APP_NAME'),
                    ],
                    "category" => optional($data->category)->name ?? null,
                    "offers" => [
                        "@type" => "Offer",
                        "url" => route('products.show', $data->id),
                        "priceCurrency" => get_system_default_currency()->code,
                        "price" => number_format($finalPrice, 2, '.', ''),
                        "priceValidUntil" => now()->addYear()->toDateString(),
                        "itemCondition" => "https://schema.org/NewCondition",
                        "availability" => ($data->stocks->first() && $data->stocks->first()->qty > 0)
                            ? "https://schema.org/InStock"
                            : "https://schema.org/OutOfStock",
                        "seller" => [
                            "@type" => "Organization",
                            "name" => env('APP_NAME'),
                        ],
                    ],
                    "aggregateRating" => [
                        "@type" => "AggregateRating",
                        "ratingValue" => $data->rating > 0 ? $data->rating : 4.5,
                        "reviewCount" => $data->reviews_count > 0 ? $data->reviews_count : 1,
                    ],
                    "review" => $data->reviews->map(function ($review) {
                        return [
                            "@type" => "Review",
                            "author" => [
                                "@type" => "Person",
                                "name" => $review->user->name ?? 'Anonymous',
                            ],
                            "datePublished" => $review->created_at->toDateString(),
                            "reviewBody" => strip_tags($review->comment ?? ''),
                            "reviewRating" => [
                                "@type" => "Rating",
                                "ratingValue" => $review->rating ?? 5,
                                "bestRating" => "5",
                            ],
                        ];
                    })->values()->toArray(),
                ];

                $schema = array_filter($schema, fn($v) => !is_null($v) && $v !== '');

                $stock_price_by_size = stock_price_by_size($data->stocks, $requestedSlug);


                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'meta_title' => $data->meta_title,
                    'meta_description' => $data->meta_description,
                    'thumbnail_image' => uploaded_asset($data->thumbnail_img),
                    'has_discount' => home_base_price($data, false) != home_discounted_base_price($data, false),
                    'discount' => "-" . discount_in_percentage($data) . "%",
                    'stroked_price' => home_base_price($data),
                    'main_price' => home_discounted_base_price($data),
                    'rating' => (float) $data->rating,
                    'product_code' =>  $data->product_code,
                    'pip_code' => $data->pip_code,
                    'vat' => round((optional($data->taxes->first())->tax / 100) * (optional($data->stocks->first())->price) ?? 0, 2),
                    'price' => optional($data->stocks->first())->price ?? '',
                    'total_price' =>  round($data->stocks->first()->price + round((optional($data->taxes->first())->tax / 100) * (optional($data->stocks->first())->price) ?? 0, 2), 2),
                    'brand' => optional($data->brand)->name ?? '',
                    'category_id' => $data->main_category ? optional($data->main_category)->id : '',
                    'category' => collect($data->main_category->getAllParents())->map(function ($parent) {
                        return [
                            'name' => $parent->name,
                            'slug' => $parent->slug,
                        ];
                    }),
                    'remaining_product' => optional($data->stocks->first())->qty ?? '',
                    'variant' => stock_price($data->stocks, $requestedSlug),
                    'variant_by_groups' => $stock_price_by_size['grouped_sizes'],
                    'flavours' => collect($data->stocks)
                        ->unique('flavour')
                        ->pluck('flavour')
                        ->filter()
                        ->values()
                        ->toArray(),
                    'colors' => collect($data->stocks)
                        ->unique('color')
                        ->pluck('color')
                        ->filter()
                        ->values()
                        ->toArray(),
                    'sizes' => collect($data->stocks)
                        ->unique('variant')
                        ->pluck('variant')
                        ->filter()
                        ->values()
                        ->toArray(),
                    'current_pack_qty' => collect(stock_price($data->stocks))
                        ->firstWhere('slug', $requestedSlug) ?? null,
                    'pack_qty' => $data->pack_qty ?? '',
                    'canonical_slug' => $data->slug ?? '',
                    'slugs' => collect(stock_price($data->stocks) ?? [])
                        ->flatMap(function ($variant) use ($data) {

                            $slugs = [];
                            $base  = $data->slug;

                            if (!empty($variant['pack_qty'])) {
                                $slugs[] = $base . '-' . $variant['pack_qty'];
                            }

                            if (!empty($variant['size']) && $variant['size'] != 0) {
                                $slugs[] = $base . '-' . strtolower(
                                    preg_replace('/[^A-Za-z0-9]+/', '-', trim($variant['size']))
                                );
                            }

                            if (!empty($variant['color'])) {
                                $slugs[] = $base . '-' . strtolower(
                                    preg_replace('/[^A-Za-z0-9]+/', '-', trim($variant['color']))
                                );
                            }

                            if (!empty($variant['flavour'])) {
                                $slugs[] = $base . '-' . strtolower(
                                    preg_replace('/[^A-Za-z0-9]+/', '-', trim($variant['flavour']))
                                );
                            }

                            return $slugs;
                        })
                        ->unique()
                        ->values()
                        ->toArray(),

                    'pack_quantities' => collect(stock_price($data->stocks) ?? [])
                        ->map(function ($variant) use ($data) {

                            // $productCode = (!empty($variant['sku']) && $variant['sku'] != 0)
                            //     ? $variant['sku']
                            //     : $variant['pip_code'];
                            
                                $productCode = (!empty($variant['sku']) && $variant['sku'] != 0)
                                ? $variant['sku']
                                : $variant['pip_code'];

                            $productCode = str_replace('/', '-', trim((string) $productCode));

                            $slugParts = [$data->slug];

                            if (!empty($variant['flavour'])) {
                                $slugParts[] = strtolower(
                                    preg_replace('/[^A-Za-z0-9]+/', '-', trim($variant['flavour']))
                                );
                            } elseif (!empty($variant['size']) && $variant['size'] != 0) {
                                $slugParts[] = strtolower(
                                    preg_replace('/[^A-Za-z0-9]+/', '-', trim($variant['size']))
                                );

                                if (!empty($variant['color'])) {
                                    $slugParts[] = strtolower(
                                        preg_replace('/[^A-Za-z0-9]+/', '-', trim($variant['color']))
                                    );
                                }
                            }

                            // Append product code at the end
                            if (!empty($productCode)) {
                                $slugParts[] = $productCode;
                            }

                            return [
                                'pack_qty' => $variant['pack_qty'],
                                'slug'     => implode('-', $slugParts),
                            ];
                        })
                        ->unique('pack_qty')
                        ->values()
                        ->toArray(),

                    'images' => collect(json_decode($data->photos, true) ?? explode(',', $data->photos))
                        ->filter()
                        ->map(function ($photo) {
                            return uploaded_asset(trim($photo));
                        })->toArray(),
                    'short_description' => $data->short_description ?? '',
                    'pharmaceutical_product' => $data->pharmaceutical_product == "1" ? 'true' : 'false',
                    'description' => $data->description ?? '',
                    'information' => $data->information ?? '',
                    'sales' => (int) $data->num_of_sale,
                    'is_wholesale' => $wholesale_product,
                    'is_best_seller' => $data->featured == "1" ? true : false,
                    'is_top_rated' => $data->todays_deal == "1" ? true : false,
                    'is_wishlist' => $data->wishlists()->where('user_id', auth()->id())->exists(),
                    'schema' => $schema,
                    'links' => [
                        'details' => route('products.show', $data->id),
                    ]
                ];
            }),
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200,
        ];
    }
}
