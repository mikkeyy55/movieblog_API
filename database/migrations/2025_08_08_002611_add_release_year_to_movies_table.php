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
            $table->year('release_year')->nullable()->after('description');
            
            // Add indexes for better search performance
            $table->index('release_year');
            $table->index('genre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropIndex(['release_year']);
            $table->dropIndex(['genre']);
            $table->dropColumn('release_year');
        });
    }
};