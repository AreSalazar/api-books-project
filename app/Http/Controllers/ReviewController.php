<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Throwable;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        //Se obtiene el libro
        $book = Book::findOrFail($id);

        //Obtiene todas las reviews del libro
        $reviews = Review::where('book_id', $book->id)->with('user')->get();

        return response()->json([
            'data' => $reviews,
            'message' => 'Review retrieved successfully',
            'status' => 'success'
        ], 200); //OK
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        //Validación de datos
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        //Se obtiene el libro
        $book = Book::findOrFail($id);

        //Se obtiene el id del usuario
        $user_id = $request->user()->id; //$user_id = Auth::id();

        //Verificación si el usuario ya dejó review en este libro
        $exists = Review::where('book_id', $book->id)->where('user_id', $user_id)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'You already reviewed this book',
                'status' => 'error'
            ], 409); //CONFLICTO
        }

        //Se crea la review por parte del usuario con su respectivo id de libro y usuario
        $review = Review::create([
            'book_id' => $book->id,
            'user_id' => $user_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        //Se muestra la review creada con mensajes de éxito
        return response()->json([
            'data' => $review,
            'message' => 'Review created successfully',
            'status' => 'success'
        ], 201); //CREADO
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $reviews)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $reviews)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //Validación de datos
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        //Se obtiene la review
        $review = Review::findOrFail($id);

        //Se obtiene el usuario
        $user_id = $request->user()->id;

        //Si el id de la review no pertenece al id del usuario
        if ($review->user_id !== $user_id) {
            return response()->json([
                'error' => 'No authorized'
            ], 403); //PROHIBIDO
        }

        //Actualización de datos parcialmente
        $review->update([
            'rating' => $request->rating ?? $review->rating,
            'comment' => $request->comment ?? $review->comment
        ]);

        //Se muestra la review actualizada con mensajes de éxito
        return response()->json([
            'data' => $review,
            'message' => 'Review updated successfully',
            'status' => 'success'
        ], 200); //OK
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        //Se obtiene la review
        $review = Review::findOrFail($id);

        //Se obtiene el usuario
        $user_id = $request->user()->id;

        //Si el id del usuario no pertenece al id de la review
        if ($review->user_id !== $user_id) {
            return response()->json([
                'error' => 'No authorized'
            ], 403); //PROHIBIDO
        }

        //Elimina la review
        $review->delete();

        //Muestra mensaje de eliminación exitosa
        return response()->json([
            'message' => 'Review deleted successfully',
            'status' => 'success'
        ], 200); //OK
    }

    public function average(string $id)
    {
        //Se obtiene el libro
        $book = Book::findOrFail($id);

        //Se obtiene la calificación del libro
        $avg_rating = Review::where('book_id', $book->id)->avg('rating'); //avg() es función SQL

        //Muestra la calificación redondeada a 2 decimales del libro
        return response()->json([
            'book_id' => $book->id,
            'avg_rating' => round($avg_rating, 2)
        ]);
    }
}
