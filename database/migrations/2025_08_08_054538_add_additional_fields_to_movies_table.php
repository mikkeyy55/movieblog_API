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
        Schema::table('movies', function (Blueprint $table) {
            $table->string('director')->nullable()->after('cover_image');
            $table->text('cast')->nullable()->after('director');
            $table->integer('runtime')->nullable()->after('cast'); // in minutes
            $table->string('rating', 10)->nullable()->after('runtime'); // e.g., PG-13, R, etc.
            $table->string('language', 100)->nullable()->after('rating');
            $table->string('country', 100)->nullable()->after('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn(['director', 'cast', 'runtime', 'rating', 'language', 'country']);
        });
    }
};
