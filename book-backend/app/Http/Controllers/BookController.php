<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the books.
     */
    public function index()
    {
        $books = Book::orderBy('created_at', 'desc')->get();
        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Store a newly created book in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'publisher' => 'nullable|string|max:255',
            'pages' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create the book
        Book::create($request->all());

        return redirect()->route('books.index')
            ->with('success', 'Book added successfully!');
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    /**
     * Update the specified book in storage.
     */
    public function update(Request $request, Book $book)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn,' . $book->id,
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'publisher' => 'nullable|string|max:255',
            'pages' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update the book
        $book->update($request->all());

        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully!');
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        
        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully!');
    }
    /**
 * Search books (AJAX autocomplete)
 */
public function search(Request $request)
{
    $query = $request->input('q', '');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $books = Book::where(function($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('author', 'like', "%{$query}%")
              ->orWhere('isbn', 'like', "%{$query}%");
        })
        ->where('quantity', '>', 0)
        ->select('id', 'title', 'author', 'price', 'quantity', 'isbn')
        ->limit(10)
        ->get();
    
    return response()->json($books);
}
}