<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\SyncService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;
    protected SyncService $syncService;

    public function __construct(CategoryService $categoryService, SyncService $syncService)
    {
        $this->categoryService = $categoryService;
        $this->syncService = $syncService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAll();

        return view('category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $category = $this->categoryService->create($validated);

        $this->syncService->log(
            'Category',
            'Berhasil',
            "CREATE - Kategori baru berhasil ditambahkan: {$validated['name']}"
        );

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $result = $this->categoryService->update($id, $validated);

        if (! $result) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $this->syncService->log(
            'Category',
            'Berhasil',
            "UPDATE - Kategori berhasil diperbarui: {$validated['name']}"
        );

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $result = $this->categoryService->delete($id);

        if (! $result) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $this->syncService->log(
            'Category',
            'Berhasil',
            "DELETE - Kategori berhasil dihapus. ID: {$id}"
        );

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}