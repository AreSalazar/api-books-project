<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void //Este método se ejecuta cuando corres php artisan migrate

    {
        Schema::create('reviews', function (Blueprint $table) { //Blueprint $table → objeto que permite definir columnas
            $table->id(); //PRIMARY KEY
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); //tinyInteger es diminuto int, de 1 a 5
            $table->text('comment')->nullable();
            $table->timestamps();
            //Crea automáticamente dos columnas: created_at y updated_at (cuándo se creó y cuándo se actualizó)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
