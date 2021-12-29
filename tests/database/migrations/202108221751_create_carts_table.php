<?php

namespace Tests\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Models\Cart;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table): void {
            $table->id();

            Cart::addSlugColumn($table);

            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('carts');
    }
};
