<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_display_books_index_page(): void
    {
        $book = Book::factory()->create(['title' => 'Belajar Laravel 12']);

        $response = $this->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertSee('Belajar Laravel 12');
    }

    public function test_can_display_create_book_page(): void
    {
        $response = $this->get(route('books.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Buku');
    }

    public function test_can_store_a_new_book(): void
    {
        $payload = [
            'title' => 'Pemrograman Web Modern',
            'author' => 'Syahrizal',
            'publisher' => 'Informatika',
            'year' => 2026,
            'isbn' => '978-602-000-111-2',
            'description' => 'Buku panduan web modern.',
        ];

        $response = $this->post(route('books.store'), $payload);

        $response->assertRedirect(route('books.index'));
        $response->assertSessionHas('success', 'Buku berhasil ditambahkan!');

        $this->assertDatabaseHas('books', [
            'title' => 'Pemrograman Web Modern',
            'author' => 'Syahrizal',
            'year' => 2026,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->post(route('books.store'), []);

        $response->assertSessionHasErrors(['title', 'author', 'year']);
    }

    public function test_can_display_edit_book_page(): void
    {
        $book = Book::factory()->create();

        $response = $this->get(route('books.edit', $book));

        $response->assertStatus(200);
        $response->assertSee($book->title);
    }

    public function test_can_update_a_book(): void
    {
        $book = Book::factory()->create([
            'title' => 'Judul Lama',
            'year' => 2020,
        ]);

        $payload = [
            'title' => 'Judul Baru Diupdate',
            'author' => $book->author,
            'year' => 2025,
        ];

        $response = $this->put(route('books.update', $book), $payload);

        $response->assertRedirect(route('books.index'));
        $response->assertSessionHas('success', 'Buku berhasil diupdate!');

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Judul Baru Diupdate',
            'year' => 2025,
        ]);
    }

    public function test_can_delete_a_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.index'));
        $response->assertSessionHas('success', 'Buku berhasil dihapus!');

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }
}
