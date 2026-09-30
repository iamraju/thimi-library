<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LibraryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_readers_can_browse_books_but_cannot_manage_them_or_users(): void
    {
        $reader = User::factory()->create(['role' => 'reader']);

        $this->actingAs($reader)->get(route('books.index'))->assertOk();
        $this->actingAs($reader)->get(route('books.create'))->assertForbidden();
        $this->actingAs($reader)->post(route('books.store'), [])->assertForbidden();
        $this->actingAs($reader)->get(route('users.index'))->assertForbidden();
    }

    public function test_librarians_can_manage_books_but_not_categories_or_publishers(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($superadmin);

        $this->post(route('categories.store'), [
            'title' => 'Technology',
            'description' => 'Technology titles',
            'status' => 1,
        ])->assertRedirect(route('categories.index'));
        $category = Category::firstOrFail();
        $this->assertSame($superadmin->id, $category->created_by);

        $this->post(route('publishers.store'), [
            'title' => 'Example Press',
            'status' => 1,
        ])->assertRedirect(route('publishers.index'));
        $publisher = Publisher::firstOrFail();

        $librarian = User::factory()->create(['role' => 'librarian']);
        $this->actingAs($librarian);

        $this->get(route('categories.index'))->assertForbidden();
        $this->get(route('categories.create'))->assertForbidden();
        $this->post(route('categories.store'), ['title' => 'Blocked', 'status' => 1])->assertForbidden();
        $this->get(route('publishers.index'))->assertForbidden();
        $this->post(route('publishers.store'), ['title' => 'Blocked', 'status' => 1])->assertForbidden();

        $this->post(route('books.store'), [
            'title' => 'Laravel Field Guide',
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'book_type' => 'new',
            'author_name' => 'A. Writer',
            'status' => 1,
        ])->assertRedirect(route('books.index'));
        $book = Book::firstOrFail();
        $this->assertSame('laravel-field-guide', $book->slug);
        $this->assertSame($librarian->id, $book->created_by);
    }

    public function test_only_superadmins_can_create_users_and_cannot_remove_themselves(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($superadmin);

        $this->post(route('users.store'), [
            'name' => 'Library Reader',
            'email' => 'reader@example.test',
            'role' => 'reader',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['email' => 'reader@example.test', 'role' => 'reader']);
        $this->delete(route('users.destroy', $superadmin))->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users', ['id' => $superadmin->id]);
    }

    public function test_book_cover_image_is_uploaded_replaced_and_removed_with_the_book(): void
    {
        Storage::fake('public');

        $librarian = User::factory()->create(['role' => 'librarian']);
        $this->actingAs($librarian);

        $category = Category::create(['title' => 'Fiction', 'status' => 1]);
        $publisher = Publisher::create(['title' => 'Fiction House', 'status' => 1]);

        $this->post(route('books.store'), [
            'title' => 'Illustrated Guide',
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'book_type' => 'new',
            'status' => 1,
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        ])->assertRedirect(route('books.index'));

        $book = Book::firstOrFail();
        $this->assertNotNull($book->cover_image);
        Storage::disk('public')->assertExists($book->cover_image);
        $originalCoverPath = $book->cover_image;

        $this->put(route('books.update', $book), [
            'title' => 'Illustrated Guide',
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'book_type' => 'new',
            'status' => 1,
            'cover_image' => UploadedFile::fake()->image('new-cover.jpg'),
        ])->assertRedirect(route('books.index'));

        $book->refresh();
        $this->assertNotSame($originalCoverPath, $book->cover_image);
        Storage::disk('public')->assertMissing($originalCoverPath);
        Storage::disk('public')->assertExists($book->cover_image);

        $coverPath = $book->cover_image;
        $this->delete(route('books.destroy', $book))->assertRedirect(route('books.index'));
        Storage::disk('public')->assertMissing($coverPath);
    }
}
