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
    Schema::create('materials', function (Blueprint $table) {
        // ID di sini adalah FK sekaligus PK yang merujuk ke class_contents
        $table->foreignId('id')->primary()->constrained('class_contents')->onDelete('cascade'); 
        $table->string('file_path')->nullable(); // [cite: 150]
        $table->string('external_link')->nullable(); // [cite: 151]
        // Timestamps tidak perlu karena ikut parent (class_contents)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
