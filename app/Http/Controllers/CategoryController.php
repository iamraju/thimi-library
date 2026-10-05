<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('catalog/categories/Index', [
            'categories' => Category::query()
                ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where('title', 'like', '%'.$request->string('search').'%'))
                ->withCount('books')->orderBy('title')->paginate(15)->withQueryString(),
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('catalog/categories/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->validated($request));

        return to_route('categories.index')->with('success', __('Category created.'));
    }

    public function edit(Category $category): Response
    {
        return Inertia::render('catalog/categories/Form', ['category' => $category]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));

        return to_route('categories.index')->with('success', __('Category updated.'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->books()->exists()) {
            return back()->withErrors(['category' => __('This category still has books assigned to it.')]);
        }

        $category->delete();

        return to_route('categories.index')->with('success', __('Category deleted.'));
    }

    /** @return array{title: string, description: ?string, status: int} */
    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:categories,title,'.$category?->id],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'integer', 'in:0,1'],
        ]);
    }
}
