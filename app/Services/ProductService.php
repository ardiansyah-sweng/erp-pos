<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getItemBySKU(string $sku)
    {
        return Product::where('sku', $sku)->first();
    }

    public function search(string $query): \Illuminate\Support\Collection
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        if ($query === '') {
            return $products;
        }

        $nq = $this->normalize($query);

        return $products->filter(function ($product) use ($query, $nq) {
            if ($product->sku && stripos($product->sku, $query) !== false) return true;
            if ($product->barcode && stripos($product->barcode, $query) !== false) return true;
            if ($this->fuzzyMatchesPhrase($nq, $product->name)) return true;
            if ($product->jenis && $this->fuzzyMatchesPhrase($nq, $product->jenis)) return true;
            return false;
        })->values();
    }

    private function normalize(string $s): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($s));
    }

    private function fuzzyMatchesPhrase(string $nq, string $phrase): bool
    {
        $nt = $this->normalize($phrase);
        if ($this->fuzzyMatches($nq, $nt)) return true;

        foreach (preg_split('/\s+/', trim($phrase)) as $word) {
            $nw = $this->normalize($word);
            if ($nw && $this->fuzzyMatches($nq, $nw)) return true;
        }

        return false;
    }

    private function fuzzyMatches(string $nq, string $nt): bool
    {
        if (!$nq || !$nt) return false;
        if (str_contains($nt, $nq)) return true;
        $threshold = max(1, (int) round(strlen($nq) * 0.4));
        return levenshtein($nq, $nt) <= $threshold;
    }
}