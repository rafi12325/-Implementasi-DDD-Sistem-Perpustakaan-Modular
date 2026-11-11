<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('isbn')->unique(); // divalidasi melalui Value Object Isbn
            $table->text('description')->nullable();
            $table->foreignId('author_id')->constrained()->onDelete('restrict');
            $table->foreignId('publisher_id')->constrained()->onDelete('restrict');
            $table->year('publication_year')->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
