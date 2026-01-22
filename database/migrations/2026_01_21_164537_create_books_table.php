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
        Schema::create('books', function (Blueprint $table) { //Blueprint $table → objeto que permite definir columnas
            $table->id();//PRIMARY KEY
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            //constrained: Indica que user_id apunta a la tabla users columna id
            //onDelete: Si se elimina un usuario, se eliminan automáticamente sus libros
            //onUpdate: Si el id del usuario cambia, se actualiza en books
            $table->foreignId('language_id')->constrained()->onDelete('restrict');//Aquí está bien porque indica que un libro solo tiene un idioma, no como categorías, restrict indica que No puedes borrar un idioma si hay libros usándolo
            //$table->foreignId('book_category_id')->constrained()->onDelete('restrict');
            //Rompe el many-to-many de la tabla book_category y hace que un libro solo tenga una sola categoría
            $table->string('title');
            $table->text('sinopsis');
            $table->string('author');
            $table->date('date');
            $table->decimal('price',8,2); 
            $table->timestamps();
            //Crea automáticamente dos columnas: created_at y updated_at (cuándo se creó y cuándo se actualizó)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void //Se ejecuta cuando corres: php artisan migrate:rollback
    //Sirve para revertir la migración
    {
        Schema::dropIfExists('books'); //Elimina la tabla books solo si existe
    }
};
