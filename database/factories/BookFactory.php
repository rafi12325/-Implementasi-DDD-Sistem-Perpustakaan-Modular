<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Catalog\Models\Book;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => ucwords($title),
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 99999),
            // ISBN-13 acak dengan checksum valid
            'isbn' => $this->generateIsbn13(),
            'description' => fake()->paragraph(),
            'publication_year' => fake()->numberBetween(2010, 2025),
            'stock' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }

    private function generateIsbn13(): string
    {
        $body = '978' . fake()->numberBetween(100000000, 999999999); // 12 digit pertama

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $body[$i] * ($i % 2 === 0 ? 1 : 3);
        }

        return $body . ((10 - ($sum % 10)) % 10);
    }
}
