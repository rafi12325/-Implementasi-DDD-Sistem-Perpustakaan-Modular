<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Anggota terhubung ke users (Identity Context) lewat FK user_id,
        // tetapi data ownership tetap milik Membership Context.
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('member_number')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            // active | borrowing | suspended
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Pivot Many-to-Many: Member <-> Role
        Schema::create('member_role', function (Blueprint $table) {
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->primary(['member_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_role');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('members');
    }
};
