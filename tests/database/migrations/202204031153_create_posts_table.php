<?php

namespace Tests\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Models\Post;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table): void {
            $table->id();

            Post::addSlugColumn($table);

            $table->string('title');
            $table->timestamps();
        });

        // To test dropping the column works well with existing data on the next migration.
        Post::factory()
            ->count(10)
            ->create();
    }

    public function down(): void
    {
        Schema::drop('posts');
    }
};
