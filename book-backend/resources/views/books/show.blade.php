@extends('layouts.app')

@section('title', $book->title)
@section('page-title', $book->title)
@section('page-subtitle', 'Book Details')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Book Details</h5>
                <div>
                    <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('books.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Book Cover -->
                    <div class="col-md-4 text-center mb-4">
                        <div class="book-cover mb-3" style="width: 200px; height: 280px; margin: 0 auto; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            @if($book->cover_image)
                                <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                     alt="{{ $book->title }}" 
                                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                            @else
                                <i class="fas fa-book fa-5x text-muted"></i>
                            @endif
                        </div>
                        <h4 class="mt-3">{{ $book->title }}</h4>
                        <p class="text-muted">by {{ $book->author }}</p>
                    </div>

                    <!-- Book Details -->
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">ISBN</label>
                                <p class="form-control-plaintext"><code>{{ $book->isbn }}</code></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Price</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-success fs-6">${{ number_format($book->price, 2) }}</span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Quantity in Stock</label>
                                <p class="form-control-plaintext">
                                    @if($book->quantity > 20)
                                        <span class="badge bg-success fs-6">{{ $book->quantity }} units</span>
                                    @elseif($book->quantity > 0)
                                        <span class="badge bg-warning fs-6">{{ $book->quantity }} units</span>
                                    @else
                                        <span class="badge bg-danger fs-6">Out of stock</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Stock Value</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info fs-6">${{ number_format($book->price * $book->quantity, 2) }}</span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Category</label>
                                <p class="form-control-plaintext">
                                    @if($book->category)
                                        <span class="badge bg-primary">{{ $book->category }}</span>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Publisher</label>
                                <p class="form-control-plaintext">{{ $book->publisher ?? 'Not specified' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Pages</label>
                                <p class="form-control-plaintext">{{ $book->pages ?? 'Not specified' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Added On</label>
                                <p class="form-control-plaintext">{{ $book->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-muted">Description</label>
                                <div class="form-control-plaintext bg-light p-3 rounded">
                                    {{ $book->description ?? 'No description available.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 border-top pt-3">
                    <div class="btn-group" role="group">
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Book
                        </a>
                        <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this book?')">
                                <i class="fas fa-trash"></i> Delete Book
                            </button>
                        </form>
                        <a href="{{ route('books.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> All Books
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection