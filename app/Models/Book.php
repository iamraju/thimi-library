<?php

namespace App\Models;

use App\Concerns\Auditable;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['title', 'slug', 'category_id', 'publisher_id', 'book_type', 'cover_image', 'editor_name', 'author_name', 'written_by', 'publish_date', 'edition', 'price', 'purchased_date', 'regd_date', 'remarks', 'status'])]
#[Appends(['cover_image_url'])]
class Book extends Model
{
    use Auditable;

    protected static function booted(): void
    {
        static::saving(function (Book $book): void {
            if (blank($book->slug)) {
                $book->slug = Str::slug($book->title);
            }
        });

        static::deleted(function (Book $book): void {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'publish_date' => 'date',
            'purchased_date' => 'date',
            'regd_date' => 'date',
            'price' => 'decimal:2',
            'status' => 'integer',
        ];
    }

    /** @return Attribute<string|null, never> */
    protected function coverImageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null);
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return BelongsTo<Publisher, $this> */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }
}
