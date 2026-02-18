<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Throwable;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$category = Category::all();

        return response()->json([
            //'data' => $category,
            'data' => Category::all(),
            'message' => 'Category retrieved successfully',
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
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            //Crea una nueva categoría en la BD
            $category = Category::create($request->all());

            return response()->json([
                'data' => $category,
                'message' => 'Category created successfully',
                'status' => 'success'
            ], 201); //CREADO

        } catch (Throwable) {
            return response()->json([
                'message' => 'Failed to create category',
                'status' => 'error'
            ], 500); //ERROR DEL SERVIDOR
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //with('book') carga la relación book
        $category = Category::with('books')->findOrFail($id); //findOrFail() ya lanza excepción 404 automáticamente

        return response()->json([
            'data' => $category,
            'message' => 'Category show successfully',
            'status' => 'success'
        ], 200); //OK
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $categories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        //Se obtiene la categoría
        $category = Category::findOrFail($id); //findOrFail() ya lanza excepción 404 automáticamente.
        //Actualiza todos los campos
        $category->update($request->all());

        return response()->json([
            'data' => $category,
            'message' => 'Category updated succesfully',
            'status' => 'success'
        ], 200); //OK
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //Elimina la categoría encontrada
        Category::findOrFail($id)->delete(); //findOrFail() ya lanza excepción 404 automáticamente.

        return response()->json([
            'message' => 'Deleted successfully'
        ], 200); //OK
    }
}
