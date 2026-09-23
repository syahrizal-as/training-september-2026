@extends('layouts.admin')
@section('content')
<div class="container-fluid">

    <h1>Daftar Buku</h1>
    <a href="{{ route('books.create') }}" class="btn btn-primary mb-2">Tambah Buku</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Penerbit</th>
                            <th>Tahun</th>
                            <th>ISBN</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td>{{ $book->title }}</td>
                            <td>{{ $book->author }}</td>
                            <td>{{ $book->publisher ?? '-' }}</td>
                            <td>{{ $book->year }}</td>
                            <td>{{ $book->isbn ?? '-' }}</td>
                            <td>
                                <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Yakin ingin menghapus buku ini?')" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data buku</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $books->links() }}
        </div>
    </div>
</div>
@endsection
