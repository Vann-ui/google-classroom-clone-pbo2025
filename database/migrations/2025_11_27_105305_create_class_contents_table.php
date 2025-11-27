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
        Schema::dropIfExists('class_contents');
    }
};
