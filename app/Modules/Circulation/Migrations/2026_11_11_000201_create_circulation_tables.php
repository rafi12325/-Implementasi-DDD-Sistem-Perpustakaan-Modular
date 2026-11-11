<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Peminjaman: aggregate root Circulation Context
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number')->unique();
            $table->foreignId('member_id')->constrained()->onDelete('restrict');
            // borrowed | returned
            $table->string('status')->default('borrowed');
            $table->timestamp('borrowed_at')->useCurrent();
            $table->timestamp('due_at');
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // DetailPeminjaman: entity dalam aggregate Loan, HasMany dari Loan.
        // Menyimpan SNAPSHOT data buku (judul, ISBN) sesuai tips modul,
        // agar tidak bergantung pada perubahan tabel books di masa depan.
        Schema::create('loan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('restrict');
            $table->string('book_title'); // snapshot
            $table->string('book_isbn');  // snapshot
            $table->integer('quantity')->default(1);
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });

        // Denda
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->integer('days_late');
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('amount', 12, 2); // dihitung otomatis oleh Value Object Denda
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fines');
        Schema::dropIfExists('loan_items');
        Schema::dropIfExists('loans');
    }
};
