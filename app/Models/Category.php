<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Book;

class Category extends Model
{
    //
    protected $fillable = [
        'name'
    ];

    public function book(){
        //Si en Book está belongsToMany(Category::class) entonces aquí es a la inversa
        //Una categoría → muchos libros
        return $this->belongsToMany(Book::class);
    }
}
