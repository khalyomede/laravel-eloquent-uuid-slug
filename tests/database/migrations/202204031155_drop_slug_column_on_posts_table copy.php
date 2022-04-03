<?php

namespace Tests\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Models\Post;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            Post::dropSlugColumn($table);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            Post::addUnconstrainedSlugColumn($table);
        });

        Schema::table('posts', function (Blueprint $table): void {
            Post::fillEmptySlugs();
            Post::constrainSlugColumn($table);
        });
    }
};
