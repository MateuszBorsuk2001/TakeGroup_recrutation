<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('trans_type');
            $table->unsignedBigInteger('trans_id');
            $table->string('language', 2);
            $table->string('field');
            $table->text('value');
            $table->timestamps();

            $table->index(['trans_type', 'trans_id', 'language']);
            $table->unique(['trans_type', 'trans_id', 'language', 'field']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
