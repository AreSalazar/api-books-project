<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    //
    protected $fillable = [
        'title',
        'sinopsis',
        'author',
        'date',
        'price',
        'image'
    ];

    public function user(){
        //Un libro → un usuario
        return $this->belongsTo(User::class);
    }

    public function review(){
        //Un libro → muchas reviews
        return $this->hasMany(Review::class);
    }
    
    public function category(){
        //Categorías tiene una una tabla pivote, siempre se usa belongsToMany
        //Un libro → muchas categorías
        return $this->belongsToMany(Category::class);
    }

    public function language(){
        //Un libro → un idioma
        return $this->belongsTo(Language::class);
    }
}
