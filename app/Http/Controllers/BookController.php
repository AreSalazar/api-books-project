<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; //Permite trabajar con archivos: guardar imágenes, borrar imágenes
use Throwable; //Permite capturar cualquier error (Exceptions + Errors)

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //withAvg carga el promedio de rating de la relación reviews
            $books = Book::with(['language', 'categories'])->withAvg('reviews', 'rating')->get();//with también carga relaciones Para que el frontend tenga todo en una sola petición.

            return response()->json([
                'data' => $books,
                'message' => 'Books retrieved succesfully',
                'status' => 'success'
            ], 200); //OK

        } catch (Throwable) {
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
        try {
            //Se validan los datos de la petición
            $request->validate([
                'title' => 'required|string|max:255',
                'sinopsis' => 'nullable|string',
                'author' => 'required|string|max:255',
                'date' => 'required|numeric',
                'price' => 'required|numeric',
                'language_id' => 'required|exists:languages,id',
                'image' => 'nullable|image|mimes:jpg,png,jpeg,webp,jfif|max:4096'
            ]); //Laravel devuelve automáticamente error 422 (Datos inválidos)

            $data = $request->only(['title', 'sinopsis', 'author', 'date', 'price','language_id']);

            //Determina si los datos cargados contienen un archivo
            if ($request->hasFile('image')) {
                //Guarda la imagen en storage/app/public/books
                $data['image'] = $request->file('image')->store('books', 'public');
            }

            //Obtiene el usuario autenticado y usa la relación hasMany(Book::class)
            $book = $request->user()->books()->create($data); //No necesitas enviar user_id desde Postman, solo los datos establecidos en $data en el body del http://localhost:8000/api/books

            return response()->json([
                'data' => $book,
                'message' => 'Book created succesfully',
                'status' => 'success'
            ], 201); //CREADO

        } catch (Throwable) {
            return response()->json([
                'message' => 'Failed to create book',
                'status' => 'error'
            ], 500); //ERROR DEL SERVIDOR
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //Se obtiene el libro
        $book = Book::findOrFail($id);

        return response()->json([
            'data' => $book,
            'message' => 'Product show succesfully',
            'status' => 'success'
        ], 200); //OK
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
        //Se obtiene el libro
        $book = Book::findOrFail($id);

        //Se validan los datos de la petición
        $request->validate([
            'title' => 'required|string|max:255',
            'sinopsis' => 'string',
            'author' => 'required|string|max:255',
            'date' => 'required|numeric',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,webp,jfif|max:4096'
        ]);

        $data = $request->only(['title', 'sinopsis', 'author', 'date', 'price']);

        //Determina si los datos cargados contienen un archivo
        if ($request->hasFile('image')) {
            //Si el libro tiene una imagen guardada Y además el archivo existe en el storage público
            if ($book->image && Storage::disk('public')->exists($book->image)) {
                //Borra el archivo físico del servidor
                Storage::disk('public')->delete($book->image);
            }

            //Guarda la imagen en storage/app/public/books
            $data['image'] = $request->file('image')->store('books', 'public');
        }

        //Se actualiza los datos del libro
        $book->update($data);

        return response()->json([
            'message' => 'Book updated successfully',
            'data' => $book,
            'status' => 'success'
        ], 200); //OK
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //Se obtiene el libro
        $book = Book::findOrFail($id);

        //Si el libro tiene una imagen guardada Y además el archivo existe en el storage público
        if ($book->image && Storage::disk('public')->exists($book->image)) {
            //Borra el archivo físico del servidor
            Storage::disk('public')->delete($book->image);
        }

        //Se elimina el libro
        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully',
            'status' => 'success'
        ], 200); //OK
    }
}
