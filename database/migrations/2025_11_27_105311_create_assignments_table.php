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
    Schema::create('assignments', function (Blueprint $table) {
        // ID di sini adalah FK sekaligus PK yang merujuk ke class_contents
        $table->foreignId('id')->primary()->constrained('class_contents')->onDelete('cascade');
        $table->dateTime('due_date'); // [cite: 162]
        $table->integer('points'); // [cite: 163]
        $table->string('attachment_path')->nullable(); // [cite: 164]
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
