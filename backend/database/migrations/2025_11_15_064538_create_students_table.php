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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('student_id')->unique();
            $table->string('class');
            $table->string('section');
            $table->string('photo')->nullable();
            $table->timestamps();

            // Index to speed up lookups by student_id
            $table->index('student_id');

            // Fulltext index for faster searching on the name field
            $table->fullText('name');

            // Composite index to optimize common listing/filter queries and ordering
            $table->index(['class', 'section', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
