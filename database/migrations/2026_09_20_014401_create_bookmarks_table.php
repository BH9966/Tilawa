<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('surah_number');
            $table->string('surah_name');
            $table->unsignedSmallInteger('ayah_number');
            $table->text('ayah_text')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'surah_number', 'ayah_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
