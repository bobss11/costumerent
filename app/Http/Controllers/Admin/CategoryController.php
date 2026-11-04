<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * List all categories with costume counts.
     */
    public function index(): View
    {
        $categories = Category::withCount('costumes')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the create form.
     */
    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category,
        ]);
    }

    /**
     * Persist a new category with an auto-generated unique slug.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', "Kategori {$validated['name']} berhasil ditambahkan.");
    }

    /**
     * Show the edit form.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update a category, regenerating the slug when the name changes.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,'.$category->id],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ];

        if ($category->name !== $validated['name']) {
            $data['slug'] = $this->uniqueSlug($validated['name'], $category->id);
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', "Kategori {$category->name} berhasil diperbarui.");
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->costumes()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', "Kategori {$category->name} tidak dapat dihapus karena masih memiliki kostum.");
        }

        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', "Kategori {$name} berhasil dihapus.");
    }

    /**
     * Build a URL-friendly slug that is unique within the categories table.
     */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 1;

        while (
            Category::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
