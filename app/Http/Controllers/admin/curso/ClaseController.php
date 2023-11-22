<?php

namespace App\Http\Controllers\admin\curso;

use Illuminate\Http\Request;
use Owenoj\LaravelGetId3\GetId3;
use Vimeo\Laravel\Facades\Vimeo;
use App\Models\Course\CourseClasse;
use App\Http\Controllers\Controller;
use App\Models\Course\CourseClasseFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Curso\Clase\CourseClaseResource;
use App\Http\Resources\Curso\Clase\CourseClaseCollection;

class ClaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $clases = CourseClasse::where("course_section_id", $request->course_section_id)
        ->orderBy("id", "asc")->get();

        return response()->json([
            "clases"=> CourseClaseCollection::make($clases),
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
        
        $clase = CourseClasse::create($request->all());
        
        foreach($request->file("files") as $key=>$file){
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            $name_file = $file->getClientOriginalName();
            $data = null;
            if(in_array(strtolower($extension), ["jpeg", "bmp","jpg","png"])){
                $data = getImageSize($file);
                
            }
            $path = Storage::putFile("clases_files", $file);

            $clase_file = CourseClasseFile::create([
                'course_clase_id' =>$clase -> id,
                'name_file' =>$name_file,
                'size' =>$size,
                'resolution' =>$data ? $data[0]."x".$data[1]: NULL,
                'file' =>$path,
                'type'  =>$extension,
            ]);
        }

        error_log($clase);
        error_log($clase_file);

        return response()->json([ 'clase'=> CourseClaseResource::make($clase)]);

    }

    public function upload_video(Request $request, $id)
    {
        $time = 0;
        error_log(json_encode($request->file('video')));
        //instantiate class with file
        $track = new GetId3($request->file('video'));
 
        //get playtime
        $time = $track->getPlaytimeSeconds();
        error_log($time);

        $response = Vimeo::upload($request->file('video'));

        $course_clase = CourseClasse::findOrFail($id);
        error_log(json_encode(explode("/", $response)));
        $vimeo_id = explode("/", $response)[2];

        $course_clase->update(["vimeo_id" -> $vimeo_id, "time" -> date("H:i:s", $time)]);

        return response()->json([ 
            // "time"=> date("H:i:s", $time),
            // "response" => $response,
            "link_vimeo"=>"https://player.vimeo.com/video/".$vimeo_id,
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
        $clase = CourseClasse::findOrFail($id);
        $clase->update($request ->all());

        return response()->json([
            'clase'=> CourseClaseResource::make($clase)
        ]);
    }
    public function addFiles(Request $request)
    {
        $clase = CourseClasse::findOrFail($request->course_clase_id);
        foreach($request->file("files") as $key=>$file){
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            $name_file = $file->getClientOriginalName();
            $data = null;
            if(in_array(strtolower($extension), ["jpeg", "bmp","jpg","png"])){
                $data = getImageSize($file);
                
            }
            $path = Storage::putFile("clases_files", $file);

            $clase_file = CourseClasseFile::create([
                'course_clase_id' =>$clase -> id,
                'name_file' =>$name_file,
                'size' =>$size,
                'resolution' =>$data ? $data[0]."x".$data[1]: NULL,
                'file' =>$path,
                'type'  =>$extension,
            ]);
        }

        return response()->json([ 'clase'=> CourseClaseResource::make($clase)]);

    }

    public function removeFiles($id)
    {
        $course_clase_file = CourseClasseFile::findOrFail($id);
        $course_clase_file->delete();

        return response()->json([ "message"=> 200]);

    }

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $clase = CourseClasse::findOrFail($id);
        $clase->delete();

        return response()->json([
            "message"=> 200
        ]);
    }
}
