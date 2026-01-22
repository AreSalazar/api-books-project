<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    //
    protected $fillable = [
        'rating',
        'comment',
        //Si el usuario puede crear una review, Laravel necesita poder asignar un user_id y un book_id
        //Si no están en $fillable No se guardan
        'user_id',
        'book_id'
    ];

    protected $casts = [
        'rating' => 'integer',
        'user_id' => 'integer',
        'book_id' => 'integer'
    ];

    public function user(){
        //Una review → un usuario
        return $this->belongsTo(User::class);//Una review pertenece a un usuario
    }
    public function book(){
        //Una review → un libro
        return $this->belongsTo(Book::class);
    }
}
