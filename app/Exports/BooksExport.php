<?php

namespace App\Exports;

use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BooksExport implements FromQuery, WithHeadings, WithMapping
{
    /** @param  array{search?: string, category_id?: string}  $filters */
    public function __construct(private readonly array $filters, private readonly bool $activeOnly) {}

    /** @return Builder<Book> */
    public function query(): Builder
    {
        $search = $this->filters['search'] ?? null;
        $categoryId = $this->filters['category_id'] ?? null;

        return Book::query()
            ->with(['category:id,title', 'publisher:id,title'])
            ->when($this->activeOnly, fn ($query) => $query->where('status', 1))
            ->when(filled($search), fn ($query) => $query->where(
                fn ($query) => $query
                    ->where('title', 'like', '%'.$search.'%')
                    ->orWhere('author_name', 'like', '%'.$search.'%')
            ))
            ->when(filled($categoryId), fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('title');
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return [
            'Title', 'Category', 'Publisher', 'Book Type', 'Author', 'Editor',
            'Written By', 'Edition', 'Publish Date', 'Price', 'Purchased Date',
            'Registration Date', 'Status',
        ];
    }

    /** @return array<int, string> */
    public function map($book): array
    {
        return [
            $book->title,
            $book->category->title,
            $book->publisher->title,
            $book->book_type === 'old' ? 'Old' : 'New',
            $book->author_name ?? '',
            $book->editor_name ?? '',
            $book->written_by ?? '',
            $book->edition ?? '',
            $book->publish_date?->format('Y-m-d') ?? '',
            $book->price ?? '',
            $book->purchased_date?->format('Y-m-d') ?? '',
            $book->regd_date?->format('Y-m-d') ?? '',
            $book->status ? 'Active' : 'Inactive',
        ];
    }
}
