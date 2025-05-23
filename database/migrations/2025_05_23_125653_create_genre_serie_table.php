<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('genre_serie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('genre_id')->constrained()->onDelete('cascade');
            $table->foreignId('serie_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['genre_id', 'serie_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('genre_serie');
    }
};
