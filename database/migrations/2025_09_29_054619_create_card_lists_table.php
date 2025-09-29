<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('position')->default(0);
            $table->foreignId('board_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_lists');
    }
};