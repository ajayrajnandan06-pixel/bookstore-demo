@extends('layouts.app')

@section('title', 'Add New Book')
@section('page-title', 'Add New Book')
@section('page-subtitle', 'Add a new book to your inventory')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Book Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('books.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label">Book Title *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Author -->
                        <div class="col-md-6 mb-3">
                            <label for="author" class="form-label">Author *</label>
                            <input type="text" class="form-control @error('author') is-invalid @enderror" 
                                   id="author" name="author" value="{{ old('author') }}" required>
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ISBN -->
                        <div class="col-md-6 mb-3">
                            <label for="isbn" class="form-label">ISBN *</label>
                            <input type="text" class="form-control @error('isbn') is-invalid @enderror" 
                                   id="isbn" name="isbn" value="{{ old('isbn') }}" required>
                            @error('isbn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">International Standard Book Number</small>
                        </div>

                        <!-- Price & Quantity -->
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price ($) *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price') }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">Quantity *</label>
                            <input type="number" min="0" 
                                   class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" name="quantity" value="{{ old('quantity', 0) }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category & Publisher -->
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-control @error('category') is-invalid @enderror" 
                                    id="category" name="category">
                                <option value="">Select Category</option>
                                <option value="Fiction" {{ old('category') == 'Fiction' ? 'selected' : '' }}>Fiction</option>
                                <option value="Non-Fiction" {{ old('category') == 'Non-Fiction' ? 'selected' : '' }}>Non-Fiction</option>
                                <option value="Science" {{ old('category') == 'Science' ? 'selected' : '' }}>Science</option>
                                <option value="Technology" {{ old('category') == 'Technology' ? 'selected' : '' }}>Technology</option>
                                <option value="History" {{ old('category') == 'History' ? 'selected' : '' }}>History</option>
                                <option value="Biography" {{ old('category') == 'Biography' ? 'selected' : '' }}>Biography</option>
                                <option value="Children" {{ old('category') == 'Children' ? 'selected' : '' }}>Children</option>
                                <option value="Education" {{ old('category') == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Business" {{ old('category') == 'Business' ? 'selected' : '' }}>Business</option>
                                <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="publisher" class="form-label">Publisher</label>
                            <input type="text" class="form-control @error('publisher') is-invalid @enderror" 
                                   id="publisher" name="publisher" value="{{ old('publisher') }}">
                            @error('publisher')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pages -->
                        <div class="col-md-6 mb-3">
                            <label for="pages" class="form-label">Number of Pages</label>
                            <input type="number" min="1" 
                                   class="form-control @error('pages') is-invalid @enderror" 
                                   id="pages" name="pages" value="{{ old('pages') }}">
                            @error('pages')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Book
                        </button>
                        <a href="{{ route('books.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection