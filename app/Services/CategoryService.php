<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getAll()
    {
        return Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update($id, array $data): ?Category
    {
        $category = Category::find($id);

        if (! $category) {
            return null;
        }

        $category->update($data);

        return $category;
    }

    public function delete($id): bool
    {
        $category = Category::find($id);

        if (! $category) {
            return false;
        }

        return $category->delete();
    }
}
