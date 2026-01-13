<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'isbn' => '9780743273565',
                'price' => 12.99,
                'quantity' => 25,
                'category' => 'Fiction',
                'publisher' => 'Scribner',
                'pages' => 180,
                'description' => 'A classic novel of the Jazz Age.',
            ],
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'isbn' => '9780061120084',
                'price' => 14.99,
                'quantity' => 18,
                'category' => 'Fiction',
                'publisher' => 'Harper Perennial',
                'pages' => 324,
                'description' => 'A novel about racial injustice in the Deep South.',
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'isbn' => '9780451524935',
                'price' => 9.99,
                'quantity' => 5,
                'category' => 'Fiction',
                'publisher' => 'Signet Classic',
                'pages' => 328,
                'description' => 'A dystopian social science fiction novel.',
            ],
            [
                'title' => 'The Lean Startup',
                'author' => 'Eric Ries',
                'isbn' => '9780307887894',
                'price' => 24.99,
                'quantity' => 12,
                'category' => 'Business',
                'publisher' => 'Crown Business',
                'pages' => 336,
                'description' => 'How today\'s entrepreneurs use continuous innovation.',
            ],
            [
                'title' => 'Sapiens: A Brief History of Humankind',
                'author' => 'Yuval Noah Harari',
                'isbn' => '9780062316097',
                'price' => 19.99,
                'quantity' => 30,
                'category' => 'History',
                'publisher' => 'Harper',
                'pages' => 464,
                'description' => 'Explores the history of humankind.',
            ],
            [
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'isbn' => '9780735211292',
                'price' => 16.99,
                'quantity' => 22,
                'category' => 'Self-Help',
                'publisher' => 'Avery',
                'pages' => 320,
                'description' => 'An easy way to build good habits.',
            ],
            [
                'title' => 'The Silent Patient',
                'author' => 'Alex Michaelides',
                'isbn' => '9781250301697',
                'price' => 15.99,
                'quantity' => 8,
                'category' => 'Thriller',
                'publisher' => 'Celadon Books',
                'pages' => 336,
                'description' => 'A psychological thriller.',
            ],
            [
                'title' => 'Educated',
                'author' => 'Tara Westover',
                'isbn' => '9780399590504',
                'price' => 18.99,
                'quantity' => 15,
                'category' => 'Biography',
                'publisher' => 'Random House',
                'pages' => 352,
                'description' => 'A memoir about a woman who leaves her survivalist family.',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }

        $this->command->info('Sample books added successfully!');
    }
}