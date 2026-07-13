<?php

namespace App\Http\Resources\Concerns;

trait BuildsProductSlug
{
    /**
     * Full slug: product-slug-size-color-flavour-SKU
     * Empty/null segments are skipped so no trailing dashes appear.
     */
    protected function buildFullSlug($stock, string $productSlug): string
    {
        $rawSku = ($stock->sku != 0 && !empty($stock->sku))
            ? $stock->sku
            : ($stock->pip_code ?? '');

        // Sanitize SKU: slashes→dashes, collapse multiple dashes, trim edge dashes
        $sku = trim(
            preg_replace('/-+/', '-', str_replace('/', '-', trim((string) $rawSku))),
            '-'
        );

        $parts = [rtrim($productSlug, '-')];

        if (!empty($stock->variant) && $stock->variant != 0) {
            $parts[] = $this->slugify($stock->variant);
        }
        if (!empty($stock->color)) {
            $parts[] = $this->slugify($stock->color);
        }
        if (!empty($stock->flavour)) {
            $parts[] = $this->slugify($stock->flavour);
        }
        if ($sku !== '') {
            $parts[] = $sku;
        }

        return implode('-', array_filter($parts, fn($p) => $p !== ''));
    }

    /**
     * Size-only slug: product-slug-size
     */
    protected function buildSizeSlug($stock, string $productSlug): string
    {
        if (empty($stock->variant) || $stock->variant == 0) {
            return rtrim($productSlug, '-');
        }
        return rtrim($productSlug, '-') . '-' . $this->slugify($stock->variant);
    }

    /**
     * Slugify a single segment:
     * - Strip inch/foot/quote marks so "16"" → "16", not "16-"
     * - Lowercase, replace non-alphanumeric runs with a single dash
     * - Trim leading/trailing dashes
     */
    protected function slugify(string $value): string
    {
        // Remove inch marks (straight and curly quotes) before replacing other symbols
        $value = str_replace(['"', "'", "\u{201C}", "\u{201D}", "\u{2018}", "\u{2019}"], '', $value);

        return trim(
            strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', trim($value))),
            '-'
        );
    }
}
