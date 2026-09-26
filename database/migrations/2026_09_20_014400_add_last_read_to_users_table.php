<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('last_read_surah')->nullable()->after('password');
            $table->unsignedSmallInteger('last_read_ayah')->nullable()->after('last_read_surah');
            $table->timestamp('last_read_at')->nullable()->after('last_read_ayah');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_read_surah', 'last_read_ayah', 'last_read_at']);
        });
    }
};
