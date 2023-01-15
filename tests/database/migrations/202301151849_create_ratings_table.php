<?php

namespace Tests\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Models\Product;
use Tests\Models\Rating;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table): void {
            $table->id();

            Rating::addSlugColumn($table);

            $table->foreignIdFor(Product::class)->constrained();
            $table->string('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('ratings');
    }
};
