<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Catalog\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Pemrograman', 'Basis Data', 'Jaringan Komputer', 'Kecerdasan Buatan',
            'Rekayasa Perangkat Lunak', 'Matematika Diskrit', 'Sistem Operasi',
            'Keamanan Informasi', 'Data Science', 'UI/UX Design',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 999999),
            'description' => fake()->sentence(),
        ];
    }
}
