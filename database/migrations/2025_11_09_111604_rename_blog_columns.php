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
        Schema::table('blogs', function (Blueprint $table) {
            $table->renameColumn('content', 'body');
            $table->renameColumn('meta_title', 'seo_title');
            $table->renameColumn('meta_description', 'seo_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->renameColumn('body', 'content');
            $table->renameColumn('seo_title', 'meta_title');
            $table->renameColumn('seo_description', 'meta_description');
        });
    }
};
