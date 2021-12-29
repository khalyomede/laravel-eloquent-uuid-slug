<?php

namespace Tests\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Models\Product;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();

            Product::addSlugColumn($table);

            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('products');
    }
};
