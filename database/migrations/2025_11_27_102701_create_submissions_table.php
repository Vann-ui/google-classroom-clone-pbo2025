<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('submissions', function (Blueprint $table) {
      $table->id();
      // Merujuk ke tabel assignments (yang mana ID-nya sama dengan class_contents)
      $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
      $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
      $table->enum('type', ['file', 'link']); // Polymorphism tipe submission [cite: 175]
      $table->string('file_path')->nullable(); // Diisi jika type = file
      $table->string('link')->nullable();      // Diisi jika type = link
      $table->integer('grade')->nullable();    // Nilai dari dosen [cite: 178]
      $table->text('feedback')->nullable();    // Feedback dosen
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('submissions');
  }
};
