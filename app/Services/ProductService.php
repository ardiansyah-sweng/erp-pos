<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService
{
    public function getItemBySKU(string $sku)
    {
        return Product::where('sku', $sku)->first();
    }

    /**
     * Fuzzy search active products by name/jenis (description), tolerant to
     * typos, vowel swaps, and missing spaces. SKU/barcode still use plain
     * substring matching so exact barcode scans stay reliable.
     */
    public function search(string $query): Collection
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $query = trim($query);

        if ($query === '') {
            return $products->values();
        }

        return $products->filter(fn (Product $product) => $this->matches($product, $query))->values();
    }

    public function searchPaginated(string $query, int $perPage, int $page, array $paginatorOptions = []): LengthAwarePaginator
    {
        $results = $this->search($query);
        $page = max(1, $page);

        return new LengthAwarePaginator(
            $results->forPage($page, $perPage)->values(),
            $results->count(),
            $perPage,
            $page,
            $paginatorOptions
        );
    }

    private function matches(Product $product, string $query): bool
    {
        if ($product->sku && stripos($product->sku, $query) !== false) {
            return true;
        }

        if ($product->barcode && stripos($product->barcode, $query) !== false) {
            return true;
        }

        $normalizedQuery = $this->normalize($query);

        if ($this->fuzzyMatchesPhrase($normalizedQuery, $product->name)) {
            return true;
        }

        return $product->description !== null
            && $this->fuzzyMatchesPhrase($normalizedQuery, $product->description);
    }

    private function fuzzyMatchesPhrase(string $normalizedQuery, string $phrase): bool
    {
        $normalizedPhrase = $this->normalize($phrase);

        if ($normalizedPhrase === '') {
            return false;
        }

        if (str_contains($normalizedPhrase, $normalizedQuery) || str_contains($normalizedQuery, $normalizedPhrase)) {
            return true;
        }

        foreach (preg_split('/\s+/', trim($phrase)) as $word) {
            $normalizedWord = $this->normalize($word);

            if ($normalizedWord !== '' && $this->fuzzyMatches($normalizedQuery, $normalizedWord)) {
                return true;
            }
        }

        return $this->fuzzyMatches($normalizedQuery, $normalizedPhrase);
    }

    private function fuzzyMatches(string $normalizedQuery, string $normalizedTarget): bool
    {
        if ($normalizedQuery === '' || $normalizedTarget === '') {
            return false;
        }

        return levenshtein($normalizedQuery, $normalizedTarget) <= $this->threshold(strlen($normalizedQuery));
    }

    private function threshold(int $length): int
    {
        return match (true) {
            $length <= 3 => 0,
            $length <= 5 => 1,
            default => 2,
        };
    }

    private function normalize(string $value): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($value));
    }
}
