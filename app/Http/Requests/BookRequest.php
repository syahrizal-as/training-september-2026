<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $book = $this->route('book');
        $bookID = $book instanceof Book ? $book->id : $book;

        return [
            'title' => 'required|string|min:3|max:255',
            'author' => 'required|string|min:2|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'required|integer|min:1900|max:'.(date('Y') + 1),
            'isbn' => 'nullable|string|max:20|unique:books,isbn,'.$bookID,
            'description' => 'nullable|string',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul buku wajib diisi',
            'title.min' => 'Judul buku minimal 3 karakter',
            'title.max' => 'Judul buku maksimal 255 karakter',
            'author.required' => 'Nama penulis wajib diisi',
            'author.min' => 'Nama penulis minimal 2 karakter',
            'author.max' => 'Nama penulis maksimal 255 karakter',
            'year.required' => 'Tahun terbit wajib diisi',
            'year.integer' => 'Tahun terbit harus berupa angka',
            'year.min' => 'Tahun terbit minimal tahun 1900',
            'year.max' => 'Tahun terbit tidak valid',
            'isbn.unique' => 'ISBN sudah terdaftar',
        ];
    }
}
