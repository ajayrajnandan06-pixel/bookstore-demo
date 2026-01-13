@extends('layouts.app')

@section('title', 'Manage Books')
@section('page-title', 'Manage Books')
@section('page-subtitle', 'Add, edit, and manage your book inventory')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Book Inventory</h5>
        <a href="{{ route('books.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Book
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($books->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-book fa-3x text-muted mb-3"></i>
                <h4>No books found</h4>
                <p class="text-muted">Start by adding your first book to the inventory.</p>
                <a href="{{ route('books.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Book
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover books-datatable" id="booksTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $book)
                            <tr>
                                <td>{{ $book->id }}</td>
                                <td>
                                    @if($book->cover_image)
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                             alt="{{ $book->title }}" 
                                             style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <div style="width: 50px; height: 70px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-book text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $book->title }}</strong>
                                    @if($book->description)
                                        <small class="d-block text-muted">{{ Str::limit($book->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $book->author }}</td>
                                <td><code>{{ $book->isbn }}</code></td>
                                <td>
                                    <span class="badge bg-success">${{ number_format($book->price, 2) }}</span>
                                </td>
                                <td>
                                    @if($book->quantity > 20)
                                        <span class="badge bg-success">{{ $book->quantity }} in stock</span>
                                    @elseif($book->quantity > 0)
                                        <span class="badge bg-warning">{{ $book->quantity }} in stock</span>
                                    @else
                                        <span class="badge bg-danger">Out of stock</span>
                                    @endif
                                </td>
                                <td>
                                    @if($book->category)
                                        <span class="badge bg-info">{{ $book->category }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('books.show', $book) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('books.edit', $book) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('books.destroy', $book) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this book?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0">
                    Showing {{ $books->count() }} books
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Total Books</h6>
                        <h3 class="mb-0">{{ $books->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-book fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Total Value</h6>
                        <h3 class="mb-0">${{ number_format($books->sum(function($book) {
                            return $book->price * $book->quantity;
                        }), 2) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Low Stock (< 10)</h6>
                        <h3 class="mb-0">{{ $books->where('quantity', '<', 10)->where('quantity', '>', 0)->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Out of Stock</h6>
                        <h3 class="mb-0">{{ $books->where('quantity', 0)->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-times-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Check if DataTable is already initialized
        if (!$.fn.DataTable.isDataTable('#booksTable')) {
            // Initialize DataTable only if not already initialized
            $('#booksTable').DataTable({
                pageLength: 10,
                ordering: true,
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search books..."
                }
            });
        }
    });
</script>
@endsection