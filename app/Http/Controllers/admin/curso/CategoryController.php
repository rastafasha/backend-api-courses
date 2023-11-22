<?php

namespace App\Http\Controllers\admin\curso;


use Validator;
use Illuminate\Http\Request;
use App\Models\Course\Category;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Curso\Category\CategoryResource;
use App\Http\Resources\Curso\Category\CategoryCollection;

class CategoryController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index( Request $request)
    {
        $search = $request->search;
        $state = $request->state;

        $categories = Category::filterAdvance($search, $state)->orderby('id', 'desc')->get();

        return response()->json([
            "categories" => CategoryCollection::make($categories)
        ]);
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
        if($request->hasFile('portada')){
            $path = Storage::putFile('categories', $request -> file('portada'));
            $request->request->add(['imagen'=>$path]);
        }

        $category = Category::create($request->all());

        return response()->json(['category'=> CategoryResource::make($category)]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        if($request->hasFile('portada')){
            //verifica si tiene imagen o no para actualizar
            if($category->imagen){
                Storage::delete($category->imagen);
            }
            $path = Storage::putFile('categories', $request -> file('portada'));
            $request->request->add(['imagen'=>$path]);
        }

        $category -> update($request->all());

        return response()->json(['category'=> CategoryResource::make($category)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 200]);
    }
}
