<?php

namespace App\Http\Controllers;

use App\Exports\BooksExport;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BookController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('catalog/books/Index', [
            'books' => Book::with(['category:id,title', 'publisher:id,title'])
                ->when($request->user()->role === 'reader', fn ($query) => $query->where('status', 1))
                ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where(fn ($query) => $query->where('title', 'like', '%'.$request->string('search').'%')->orWhere('author_name', 'like', '%'.$request->string('search').'%')))
                ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
                ->orderBy('title')->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('title')->get(['id', 'title']),
            'filters' => $request->only('search', 'category_id'),
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $filters = $request->only('search', 'category_id');

        return Excel::download(
            new BooksExport($filters, $request->user()->role === 'reader'),
            'books-'.now()->format('Y-m-d-His').'.xlsx',
        );
    }

    public function create(): Response
    {
        return Inertia::render('catalog/books/Form', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        unset($data['cover_image']);
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('book-covers', 'public');
        }
        Book::create($data);

        return to_route('books.index')->with('success', 'Book created.');
    }

    public function edit(Book $book): Response
    {
        return Inertia::render('catalog/books/Form', [...$this->formOptions(), 'book' => $book]);
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $this->validated($request);
        if ($book->title !== $data['title']) {
            $data['slug'] = $this->uniqueSlug($data['title'], $book);
        }
        unset($data['cover_image']);
        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('book-covers', 'public');
        }
        $book->update($data);

        return to_route('books.index')->with('success', 'Book updated.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return to_route('books.index')->with('success', 'Book deleted.');
    }

    /** @return array<string, mixed> */
    private function formOptions(): array
    {
        return [
            'categories' => Category::orderBy('title')->get(['id', 'title']),
            'publishers' => Publisher::orderBy('title')->get(['id', 'title']),
        ];
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'publisher_id' => ['required', 'integer', 'exists:publishers,id'],
            'book_type' => ['required', 'string', 'in:new,old'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'editor_name' => ['nullable', 'string', 'max:255'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'written_by' => ['nullable', 'string', 'max:255'],
            'publish_date' => ['nullable', 'date'],
            'edition' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'purchased_date' => ['nullable', 'date'],
            'regd_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'integer', 'in:0,1'],
        ]);
    }

    private function uniqueSlug(string $title, ?Book $book = null): string
    {
        $base = Str::slug($title) ?: 'book';
        $slug = $base;
        $suffix = 2;

        while (Book::where('slug', $slug)->when($book, fn ($query) => $query->whereKeyNot($book->id))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
