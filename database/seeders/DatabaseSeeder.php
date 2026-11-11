<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Catalog\Models\Author;
use Modules\Catalog\Models\Book;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Publisher;
use Modules\Membership\Models\Member;
use Modules\Membership\Models\Membership;
use Modules\Membership\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ---------------- Auth / Identity ---------------- */
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $regular = User::create([
            'name' => 'User Biasa',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        /* ---------------- Membership Context ---------------- */
        $adminRole = Role::create(['name' => 'Administrator Perpustakaan', 'slug' => 'admin']);
        $regularRole = Role::create(['name' => 'Anggota Reguler', 'slug' => 'member']);

        $adminMember = Member::create([
            'user_id' => $admin->id,
            'member_number' => 'MBR-00001',
            'status' => 'active',
        ]);
        $adminMember->roles()->attach($adminRole);

        $regularMember = Member::create([
            'user_id' => $regular->id,
            'member_number' => 'MBR-00002',
            'status' => 'active',
        ]);
        $regularMember->roles()->attach($regularRole);

        // Keanggotaan (entity dalam aggregate Member)
        Membership::create([
            'member_id' => $adminMember->id,
            'type' => 'premium',
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addYear(),
        ]);
        Membership::create([
            'member_id' => $regularMember->id,
            'type' => 'regular',
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        // Beberapa anggota tambahan untuk testing
        $extraMembers = Member::factory()->count(3)->create();

        /* ---------------- Catalog Context ---------------- */
        $authors = Author::factory()->count(5)->create();
        $publishers = Publisher::factory()->count(3)->create();

        $parentCategories = Category::factory()->count(4)->create();
        foreach ($parentCategories as $category) {
            Category::factory()->count(2)->create(['parent_id' => $category->id]); // sub-kategori
        }
        $allCategories = Category::all();

        $books = Book::factory()->count(20)->make()->each(function ($book) use ($authors, $publishers, $allCategories) {
            $book->author_id = $authors->random()->id;
            $book->publisher_id = $publishers->random()->id;
            $book->save();

            // Many-to-Many pivot dengan added_at
            $book->categories()->attach(
                $allCategories->random(rand(1, 3))->pluck('id')->toArray(),
                ['added_at' => now()]
            );
        });

        // Satu buku pasti tersedia agar mudah diuji peminjamannya
        $demo = Book::create([
            'title' => 'Pemrograman Web Backend dengan Laravel',
            'slug' => 'pemrograman-web-backend-dengan-laravel',
            'isbn' => '9786020312347',
            'description' => 'Buku contoh untuk pengujian peminjaman.',
            'author_id' => $authors->first()->id,
            'publisher_id' => $publishers->first()->id,
            'publication_year' => 2025,
            'stock' => 3,
            'is_active' => true,
        ]);
        $demo->categories()->attach([$allCategories->first()->id], ['added_at' => now()]);
        $books->push($demo);
    }
}
