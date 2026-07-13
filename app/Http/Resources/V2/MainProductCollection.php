<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MainProductCollection extends ResourceCollection
{
    protected $requestedSlug;
    protected $relatedProducts;

    public function __construct($resource, $requestedSlug = null, $relatedProducts = null)
    {
        parent::__construct($resource);
        $this->requestedSlug = $requestedSlug;
        $this->relatedProducts = $relatedProducts ?? collect();
    }

    /**
     * Normalize slug for safe comparison
     */
    private function normalizeSlug(?string $slug): string
    {
        if (empty($slug)) return '';
        $slug = preg_replace('/-[A-Z0-9\-]+$/i', '', $slug);
        $slug = str_replace(' ', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return strtolower(trim($slug));
    }

    public function toArray($request)
    {
        $requestedSlug = $this->requestedSlug;
        $relatedProducts = $this->relatedProducts;

        return [
            'data' => $this->collection->map(function ($data) use ($requestedSlug) {

                $wholesale_product = ($data->wholesale_product == 1);
                $stockData = collect(stock_price($data->stocks, $requestedSlug));

                $variantsNested = [];
                foreach ($stockData as $variant) {
                    $size    = $variant['size'] ?? 'default';
                    $color   = $variant['color'] ?? null;
                    $flavour = $variant['flavour'] ?? null;

                    if (!isset($variantsNested[$size])) {
                        $sizeSlug = strtolower(str_replace(' ', '-', $size));
                        $variantsNested[$size] = [
                            'slug'         => $data->slug . '-' . $sizeSlug,
                            'size'         => $size,
                            'current_slug' => false,
                            'colors'       => [],
                            'flavours'     => [],
                            "sizes"       => [],
                        ];
                    }

                    if ($flavour) {
                        $variantsNested[$size]['flavours'][] = $variant;
                    } elseif($color) {
                        $variantsNested[$size]['colors'][] = $variant;
                    }else{
                        $variantsNested[$size]['sizes'][] = $variant;

                    }
                }


                foreach ($variantsNested as $sk => $sizeGroup) {
                    $variantsNested[$sk]['current_slug'] = false;
                    foreach ($variantsNested[$sk]['colors'] as $vi => $v) {
                        $variantsNested[$sk]['colors'][$vi]['current_slug'] = false;
                    }
                    foreach ($variantsNested[$sk]['flavours'] as $vi => $v) {
                        $variantsNested[$sk]['flavours'][$vi]['current_slug'] = false;
                    }
                    foreach ($variantsNested[$sk]['sizes'] as $vi => $v) {
                        $variantsNested[$sk]['sizes'][$vi]['current_slug'] = false;
                    }
                }

                if ($requestedSlug) {
                    $matchFound = false;

                    foreach ($variantsNested as $sk => $sizeGroup) {
                        foreach (['colors', 'flavours', 'sizes'] as $type) {
                            foreach ($variantsNested[$sk][$type] as $vi => $variant) {
                                if ($requestedSlug === $variant['slug']) {
                                    $variantsNested[$sk][$type][$vi]['current_slug'] = true;
                                    $variantsNested[$sk]['current_slug'] = true;
                                    $matchFound = true;
                                    break 3;
                                }
                            }
                        }
                    }

                    if (!$matchFound) {
                        foreach ($variantsNested as $sk => $sizeGroup) {
                            if ($requestedSlug === $sizeGroup['slug']) {
                                $variantsNested[$sk]['current_slug'] = true;
                                $matchFound = true;
                                break;
                            }
                        }
                    }
                }

                $variantsNested = array_values($variantsNested);

                $selectedVariant = null;
                foreach ($variantsNested as $sizeGroup) {
                    if ($sizeGroup['current_slug']) {
                        foreach ($sizeGroup['colors'] as $v) {
                            if (!empty($v['current_slug'])) {
                                $selectedVariant = $v;
                                goto price_done;
                            }
                        }
                        foreach ($sizeGroup['flavours'] as $v) {
                            if (!empty($v['current_slug'])) {
                                $selectedVariant = $v;
                                goto price_done;
                            }
                        }
                        foreach ($sizeGroup['sizes'] as $v) {
                            if (!empty($v['current_slug'])) {
                                $selectedVariant = $v;
                                goto price_done;
                            }
                        }
                        if (!empty($sizeGroup['colors'])) {
                            $selectedVariant = $sizeGroup['colors'][0];
                            goto price_done;
                        }
                        if (!empty($sizeGroup['flavours'])) {
                            $selectedVariant = $sizeGroup['flavours'][0];
                            goto price_done;
                        }
                        if (!empty($sizeGroup['sizes'])) {
                            $selectedVariant = $sizeGroup['sizes'][0];
                            goto price_done;
                        }
                    }
                }
                price_done:

                if (!$selectedVariant && !empty($variantsNested)) {
                    $first = reset($variantsNested);
                    $selectedVariant = $first['colors'][0] ?? $first['flavours'][0] ?? $first['sizes'][0] ?? null;
                }

                $price = $selectedVariant['net_price'] ?? 0;
                
                $discount_price = $selectedVariant['price'] ?? 0;

                // $vat = ((optional($data->taxes->first())->tax ?? 0) / 100) * $price;
                $vat = ((optional($data->taxes->first())->tax ?? 0) / 100) * $discount_price;
                $finalPrice = $selectedVariant['total_price'] ?? 0;

                $placeholder = static_asset('assets/img/placeholder.jpg');

                $images = collect(json_decode($data->photos, true) ?? explode(',', $data->photos))
                    ->filter(fn($photo) => !empty(trim((string) $photo)))
                    ->map(fn($photo) => uploaded_asset(trim($photo)))
                    ->filter(fn($url) => $url !== $placeholder)
                    ->values()
                    ->toArray();

                $sizes    = $data->stocks->pluck('variant')->filter()->unique()->values()->toArray();
                $colors   = $data->stocks->pluck('color')->filter()->unique()->values()->toArray();
                $flavours = $data->stocks->pluck('flavour')->filter()->unique()->values()->toArray();

                $variantMap = [];
                foreach ($data->stocks as $stock) {
                    $size = $stock->variant;
                    if (!$size) continue;
                    if (!isset($variantMap[$size])) {
                        $variantMap[$size] = ['colors' => [], 'flavours' => []];
                    }
                    if ($stock->color) $variantMap[$size]['colors'][] = $stock->color;
                    if ($stock->flavour) $variantMap[$size]['flavours'][] = $stock->flavour;
                }
                $variantMap = collect($variantMap)->map(fn($item) => [
                    'colors'   => array_values(array_unique($item['colors'])),
                    'flavours' => array_values(array_unique($item['flavours']))
                ]);

                $schema = [
                    "@context" => "https://schema.org/",
                    "@type" => "Product",
                    "name" => $data->meta_title ?? $data->getTranslation('name'),
                    "image" => array_merge([uploaded_asset($data->thumbnail_img)], $images),
                    "description" => strip_tags($data->short_description ?: $data->description),
                    "sku" => $data->pip_code ?: $data->product_code,
                    "mpn" => (string) $data->id,
                    "brand" => ["@type" => "Brand", "name" => optional($data->brand)->name ?? env('APP_NAME')],
                    "offers" => [
                        "@type" => "Offer",
                        "url" => route('products.show', $data->id),
                        "priceCurrency" => get_system_default_currency()->code,
                        "price" => number_format($finalPrice, 2, '.', ''),
                        "priceValidUntil" => now()->addYear()->toDateString(),
                        "itemCondition" => "https://schema.org/NewCondition",
                        "availability" => ($selectedVariant['qty'] ?? 0) > 0 ? "https://schema.org/InStock" : "https://schema.org/OutOfStock",
                        "seller" => ["@type" => "Organization", "name" => env('APP_NAME')],
                    ],
                ];
                $schema = array_filter($schema, fn($v) => !is_null($v) && $v !== '');

                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'meta_title' => $data->meta_title,
                    'meta_description' => $data->meta_description,
                    'thumbnail_image' => uploaded_asset($data->thumbnail_img),
                    'has_discount' => home_base_price($data, false) != home_discounted_base_price($data, false),
                    'discount' => "-" . discount_in_percentage($data) . "%",
                    'rating' => (float) $data->rating,
                    'product_code' => $data->product_code,
                    'pip_code' => $data->pip_code,
                    'vat' => single_price($vat),
                    'net_price' => single_price($price),
                    'price' => single_price($discount_price), //$price,
                    'total_price' => single_price($finalPrice),
                    'brand' => optional($data->brand)->name ?? '',
                    'category_id' => $data->main_category ? optional($data->main_category)->id : '',
                    'category' => collect($data->main_category->getAllParents())->map(fn($parent) => [
                        'name' => $parent->name,
                        'slug' => $parent->slug,
                    ]),
                    'remaining_product' => $selectedVariant['qty'] ?? '',
                    'pack_qty' => $data->pack_qty ?? '',
                    'canonical_slug' => $data->slug ?? '',
                    'images' => $images,
                    'short_description' => $data->short_description ?? '',
                    'pharmaceutical_product' => $data->pharmaceutical_product == "1" ? 'true' : 'false',
                    'description' => $data->description ?? '',
                    'information' => $data->information ?? '',
                    'sales' => (int) $data->num_of_sale,
                    'faq'   => !empty($data->faq) ? (json_decode($data->faq, true) ?? []) : [],
                    'is_wholesale' => $wholesale_product,
                    'is_best_seller' => $data->featured == "1",
                    'is_top_rated' => $data->todays_deal == "1",
                    'is_wishlist' => ($uid = auth('sanctum')->id()) ? $data->wishlists()->where('user_id', $uid)->exists() : false,
                    // 'product_stocks' => stock_price($data->stocks, $requestedSlug),
                    'variants_nested' => $variantsNested,
                    'attributes' => [
                        ['name' => 'size', 'values' => $sizes],
                        ['name' => 'color', 'values' => $colors],
                        ['name' => 'flavour', 'values' => $flavours],
                    ],
                    'variant_map' => $variantMap,
                    'schema' => $schema,
                    
                ];
            })->first(),
            'related_products' => $relatedProducts->map(function ($item) {
                $stock = $item->stocks->sortBy('price')->first();
                $price = $stock->price ?? 0;
                $taxRate = (optional($item->taxes->first())->tax ?? 0) / 100;
                $vat   = round($taxRate * $price, 2);
                return [
                    'id'              => $item->id,
                    'name'            => $item->name,
                    'canonical_slug'            => $item->slug,
                    'thumbnail_image' => uploaded_asset($item->thumbnail_img),
                    'product_code'             => $stock->sku ?? $item->product_code,
                    'pip_code'        => $item->pip_code,
                    'price'           => single_price($price),
                    'vat'             => single_price($vat),
                    'total_price'     => single_price($price + $vat),
                    'has_discount'    => home_base_price($item, false) != home_discounted_base_price($item, false),
                    'discount'        => '-' . discount_in_percentage($item) . '%',
                    'rating'          => (float) $item->rating,
                    'brand'           => optional($item->brand)->name ?? '',
                    'category'        => optional($item->main_category)->name ?? '',
                    'is_wishlist'     => ($uid = auth('sanctum')->id()) ? $item->wishlists()->where('user_id', $uid)->exists() : false,
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
