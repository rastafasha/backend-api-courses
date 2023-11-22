<?php

namespace App\Http\Controllers\admin\curso;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Course\CourseSection;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sections = CourseSection::withCount("classes")
        ->where("course_id", $request->course_id)
        ->orderBy("id", "asc")->get();
        
        return response()->json([
            "sections"=> $sections
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
        $section = CourseSection::create($request->all());

        return response()->json([
            "section"=> $section
        ]);

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
        $section = CourseSection::findOrFail($id);
        $section->update($request ->all());

        return response()->json([
            "section"=> $section
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $section = CourseSection::findOrFail($id);
        //muy importante
        if($section->classes->count() > 0){
            return response()->json([
                "message" => 403,
                "message_text"=>"No puedes eliminar esta sección porque tiene clases"
            ] );
        }

        $section->delete();

        return response()->json([
            "message"=> 200
        ]);
    }
}
