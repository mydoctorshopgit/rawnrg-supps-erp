<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

use App\Http\Resources\Concerns\BuildsProductSlug;

class SearchProductCollection extends ResourceCollection
{
    use BuildsProductSlug;
    protected string $searchTerm;

    public function __construct($resource, string $searchTerm = '')
    {
        parent::__construct($resource);
        $this->searchTerm = strtolower(trim($searchTerm));
    }

    /**
     * Now each item in the collection is a ProductStock with a ->product relation.
     */
    public function toArray($request): array
    {
        $items = [];

        foreach ($this->collection as $stock) {
            $product = $stock->product;

            if (!$product) continue;

            $taxRate    = (optional($product->taxes->first())->tax ?? 0) / 100;
            $isWishlist = $product->wishlists->isNotEmpty();
            $price      = (float) $stock->price;

            $discount = 0;
            if ($product->discount_type !== null && $product->discount !== null) {
                if ($product->discount_type === 'amount') {
                    $discount = $product->discount;
                } else {
                    $discount = ($price * $product->discount) / 100;
                }
            }

            $discount_price = $price - $discount;

            $vat        = round($taxRate * $price, 2);

            $placeholder = static_asset('assets/img/placeholder.jpg');

            $stockThumb = uploaded_asset($stock->thumbnail_img);
            $thumbnail  = ($stockThumb && $stockThumb !== $placeholder)
                ? $stockThumb
                : uploaded_asset($product->thumbnail_img);

            $items[] = [
                'name'              => $product->name,
                'product_code'      => $stock->sku ?: $stock->pip_code,
                'thumbnail_image'   => $thumbnail,
                'net_price'         => $price,
                'price'             => $discount_price,
                'vat'               => $vat,
                'total_price'       => round($discount_price + $vat, 2),
                'in_stock'          => $stock->qty > 0,
                'qty'               => (int) $stock->qty,
                'slug'              => $this->buildFullSlug($stock, $product->slug),
                'size_slug'         => $this->buildSizeSlug($stock, $product->slug),
                'canonical_slug'    => $product->slug,
                'stock_id'          => $stock->id,
                'product_id'        => $product->id,
                'size'              => $stock->variant,
                'color'             => $stock->color,
                'flavour'           => $stock->flavour,
                'pack_qty'          => $stock->pack_qty,
                'sku'               => $stock->sku,
                'pip_code'          => $stock->pip_code,
                'brand'             => optional($product->brand)->name ?? '',
                'category_id'       => optional($product->main_category)->id,
                'rating'            => (float) $product->rating,
                'has_discount'      => home_base_price($product, false) != home_discounted_base_price($product, false),
                'discount'          => '-' . discount_in_percentage($product) . '%',
                'is_best_seller'    => (bool) $product->featured,
                'is_top_rated'      => (bool) $product->todays_deal,
                'is_trending'       => (bool) $product->is_trending,
                'is_save_big'       => (bool) ($product->save_big ?? false),
                'is_wishlist'       => $isWishlist,
                'short_description' => $stock->short_description ?: ($product->short_description ?? ''),
                'images'            => $this->formatImages($stock->photos, $product->photos),
            ];
        }

        return ['data' => $items];
    }

    // ── Slug helpers moved to BuildsProductSlug trait ────────────────────────

    private function formatImages(?string $stockPhotos, ?string $productPhotos = null): array
    {
        $placeholder = static_asset('assets/img/placeholder.jpg');

        $resolve = fn(?string $raw) => collect(json_decode($raw, true) ?? explode(',', (string) $raw))
            ->filter(fn($p) => !empty(trim((string) $p)))
            ->map(fn($p) => uploaded_asset(trim($p)))
            ->filter(fn($url) => $url !== $placeholder)
            ->values()
            ->toArray();

        $images = $resolve($stockPhotos);

        // Fall back to product-level photos if stock has none
        return !empty($images) ? $images : $resolve($productPhotos);
    }

    public function with($request): array
    {
        return ['success' => true, 'status' => 200];
    }
}
