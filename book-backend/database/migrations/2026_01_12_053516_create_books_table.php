<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->unique();
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('publisher')->nullable();
            $table->integer('pages')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();
            $table->softDeletes(); // For soft deletion
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
};