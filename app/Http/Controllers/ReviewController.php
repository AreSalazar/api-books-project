<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        //
        try {
            Book::findOrFail($id);

            $reviews = Review::where('book_id', $id)->with('user')->get();

            return response()->json([
                'data' => $reviews,
                'message' => 'Review retrieved successfully',
                'status' => 'success'
            ], 200); //OK

        } catch (Exception $error) {
            return response()->json([
                'message' => 'Failed to retrieved reviews',
                'error' => $error->getMessage()
            ], 500); //ERROR DEL SERVIDOR
        }
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
        //
        try {
            //Validación de datos
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string'
            ]);

            //Se obtiene el id del libro
            $book_id = Book::findOrFail($id);

            //Se obtiene el id del usuario
            $user_id = $request->user()->id;

            //Se crea la review con su respectivo id de libro y usuario
            $review = Review::create([
                'book_id' => $book_id,
                'user_id' => $user_id,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);

            //Se muestra la review creada con mensajes de éxito
            return response()->json([
                'data' => $review,
                'message' => 'Review created successfully',
                'status' => 'success'
            ],201); //CREADO

        } catch (Exception $error) {
            //Se muestra mensajes de error al fallar la creación de la review
            return response()->json([
                'message' => 'Failed to create review',
                'error' => $error->getMessage(),
            ],500); //ERROR DEL SERVIDOR
        }
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
        //
        try{
            //Validación de datos
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string'
            ]);

            //Se obtiene la review
            $review_id = Review::findOrFail($id);

            //Si la review no existe
            if(!$review_id){
                return response()->json([
                    'error' => 'Review not found'
                ],404); //NO ENCONTRADO 
            }

            //Se obtiene el usuario
            $user_id = $request->user()->id;

            //Si el token se venció
            if(!$user_id){
                return response()->json([
                    'error' => 'Token expired'
                ],401); //NO AUTORIZADO
            }

            //Si el id de la review no pertenece al id del usuario
            if($review_id->user !== $user_id){
                return response()->json([
                    'error' => 'No authorized'
                ],403); //PROHIBIDO
            }

            //Actualización de datos
            $review_id->update([
                'rating' => $request->rating ?? $review_id->rating,
                'comment' => $request->comment ?? $review_id->comment
            ]);

            //Se muestra la review actualizada con mensajes de éxito
            return response()->json([
                'message' => 'Review updated successfully',
                'data' => $review_id,
                'status' => 'success'
            ],200); //OK

        }catch(Exception $error){
            //Se muestra mensajes de error al fallar la actualización
            return response()->json([
                'message' => 'Failed to update review',
                'error' => $error->getMessage()
            ],500); //ERROR DEL SERVIDOR
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        //
        try{
            //Se obtiene la review
            $review_id = Review::findOrFail($id);

            //Se obtiene el usuario
            $user_id = $request->user()->id;

            //Si el id del usuario no pertenece al id de la review
            if($review_id->user !== $user_id){
                return response()->json([
                    'error' => 'No authorized'
                ],403); //PROHIBIDO
            }

            //Elimina la review
            $review_id->delete();

            //Muestra mensaje de eliminación exitosa
            return response()->json([
                'message' => 'Review deleted successfully',
                'status' => 'success'
            ],200); //OK

        }catch(Exception $error){
            return response()->json([
                'message' => 'Failed to delete review',
                'error' => $error->getMessage()
            ],500); //ERROR DEL SERVIDOR
        }
    }
}
