<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['teacher', 'student']); // [cite: 87]
            $table->timestamps(); // created_at, updated_at
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade'); // [cite: 102]
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('code')->unique(); // [cite: 105]
            $table->timestamps();
        });

        Schema::create('discussions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
        $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
        $table->string('title');
        $table->text('content'); // Menggunakan text agar lebih panjang
        $table->timestamps();
        });

        Schema::create('class_contents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
        $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // [cite: 137]
        $table->string('title');
        $table->text('description')->nullable();
        $table->enum('content_type', ['material', 'assignment']); // Pembeda tipe [cite: 136]
        $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
