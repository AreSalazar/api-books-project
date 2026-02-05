<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; //Para las imágenes de libros, usado en update
use Throwable;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $books = Book::withAvg('reviews', 'rating')->get();

            return response()->json([
                'data' => $books,
                'message' => 'Books retrieved succesfully',
                'status' => 'success'
            ], 200); //OK

        } catch (Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => 'Failed to retrieve books',
                'status' => 'error'
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
    public function store(Request $request)
    {
        //
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'sinopsis' => 'string',
                'author' => 'required|string|max:255',
                'date' => 'required|numeric',
                'price' => 'required|numeric',
                'image' => 'nullable|image|mimes:jpg,png,jpeg,webp,jfif|max:4096'
            ]);

            //preguntar a chat gpt ¿por qué no poner $data = Book::create($request->all()); en vez de:
            $data = $request->only(['title', 'sinopsis', 'author', 'date', 'price']);

            //Determina si los datos cargados contienen un archivo
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('books', 'public');
            }

            $book = $request->user()->books()->create($data); //gracias a esto, no tengo que poner el user_id, solo los datos establecidos en $data en el body del http://localhost:8000/api/books

            return response()->json([
                'data' => $book,
                'message' => 'Book created succesfully',
                'status' => 'success'
            ], 201); //CREADO

        } catch (Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => 'Failed to create book',
                'status' => 'error'
            ], 500); //ERROR DEL SERVIDOR
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $id)
    {
        //
        try {
            $book = Book::findOrFail($id);

            return response()->json([
                'data' => $book,
                'message' => 'Product show succesfully',
                'status' => 'success'
            ], 200); //OK

        } catch (Throwable $th) {
            return response()->json([
                'message' => 'Failed to show book',
                'status' => 'error'
            ], 500); //ERROR DEL SERVIDOR
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $books)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {
            $book = Book::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'sinopsis' => 'string',
                'author' => 'required|string|max:255',
                'date' => 'required|numeric',
                'price' => 'required|numeric',
                'image' => 'nullable|image|mimes:jpg,png,jpeg,webp,jfif|max:4096'
            ]);

            $data = $request->only(['title', 'sinopsis', 'author', 'date', 'price']);

            if ($request->hasFile('image')) {
                if ($book->image && Storage::disk('public')->exists($book->image)) {
                    Storage::disk('public')->delete($book->image);
                }

                $data['image'] = $request->file('image')->store('books', 'public');
            }

            $book->update($data);

            return response()->json([
                'message' => 'Book updated successfully',
                'data' => $book, //lleva 'data' ya que $book tiene una variable, por eso delete no tiene 'data'
                'status' => 'success'
            ], 200); //OK

        } catch (Throwable $th) {
            return response()->json([
                'message' => 'Failed to update book',
                'status' => 'error'
            ], 500); //ERROR DEL SERVIDOR
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $id)
    {
        //
        try {
            $book = Book::findOrFail($id);

            if ($book->image && Storage::disk('public')->exists($book->image)) {
                Storage::disk('public')->delete($book->image);
            }

            $book->delete();

            return response()->json([
                'message' => 'Book deleted successfully',
                'status' => 'success'
            ], 200); //OK

        } catch (Throwable $th) {
            return response()->json([
                'message' => 'Failed to delete book',
                'status' => 'error'
            ], 500); //ERROR DEL SERVIDOR
        }
    }
}
