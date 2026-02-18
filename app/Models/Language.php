<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Book;

class Language extends Model
{
    //
    protected $fillable = [
        'name',
        'code'
    ];

    public function books(){
        //Un lenguaje → muchos libros
        return $this->hasMany(Book::class);
    }
}
