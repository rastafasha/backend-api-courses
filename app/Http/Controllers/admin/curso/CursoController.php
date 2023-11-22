<?php

namespace App\Http\Controllers\admin\curso;

use Validator;
use App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\Course\Course;
use App\Models\Course\Category;
use Owenoj\LaravelGetId3\GetId3;
use Vimeo\Laravel\Facades\Vimeo;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Curso\CourseResource;
use App\Http\Resources\Curso\CourseCollection;

class CursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request)
    {
        $search = $request->search;
        $state = $request->state;
        
        $courses = Course::filterAdvance($search, $state)->orderby('id', 'desc')->get();

        return response()->json([
            "courses" => CourseCollection::make($courses)
        ]);
    }

    public function config( Request $request)
    {
        
        $categories = Category::where("category_id",NULL)->orderBy("id","desc")->get();
        $subcategories = Category::where("category_id","<>",NULL)->orderBy("id","desc")->get();
    
        $instructores = User::where("isInstructor",1)->orderBy("id","desc")->get();
        return response()->json([
            "categories" => $categories,
            "subcategories" => $subcategories,
        
            "instructors" => $instructores->map(function($instructor){
                return [
                    "id" => $instructor->id,
                    "full_name" => $instructor->name.' '.$instructor->surname,
                ];
            }),
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
        $if_exist = Course::where('title', $request->title)->first();
        if($if_exist){
            return response()->json([ 
                'message'=> 403,
                'message_text'=>'Ya existe un curso con este titulo'
            ]);
        }
        if($request->hasFile('portada')){
            $path = Storage::putFile('courses', $request -> file('portada'));
            $request->request->add(['imagen'=>$path]);
        }

        $request ->request->add(['slug' => Str::slug($request->title)]);
        $request ->request->add(['who_is_it_for' => json_encode(explode(',', $request->who_is_it_for))]);
        $request ->request->add(['requirements' => json_encode(explode(',', $request->requirements))]);
        
        $course = Course::create($request->all());
        // 'course'=> CourseResource::make($course)
        return response()->json([ 'message'=> 200]);

    }

    public function upload_video(Request $request, $id)
    {
        $time = 0;
        
        //instantiate class with file
        $track = new GetId3($request->file('video'));

        //get playtime
        $time = $track->getPlaytimeSeconds();

        $response = Vimeo::upload($request->file('video'));
        // importas el Log
        Log::info(json_encode($response));

        $course = Course::findOrFail($id);
        error_log(json_encode(explode("/",$response)));
        $vimeo_id = explode("/",$response)[2];

        $course->update(["vimeo_id" => $vimeo_id,"time" => date("H:i:s",$time)]);

        return response()->json([
            "link_video" => "https://player.vimeo.com/video/".$vimeo_id,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = Course::findOrFail($id);
        return response()->json([
            'course'=>CourseResource::make($course)
        ]);
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
        $if_exist = Course::where('id','<>',$id)->where('title', $request->title)->first();
        if($if_exist){
            return response()->json([ 
                'message'=> 403,
                'message_text'=>'Ya existe un curso con este titulo'
            ]);
        }
        $course = Course::findOrFail($id);
        if($request->hasFile('portada')){
            //verifica si tiene imagen o no para actualizar
            if($course->imagen){
                Storage::delete($course->imagen);
            }
            $path = Storage::putFile('courses', $request -> file('portada'));
            $request->request->add(['imagen'=>$path]);
        }

        $request ->request->add(['slug' => Str::slug($request->title)]);
        $request ->request->add(['who_is_it_for' => json_encode(explode(',', $request->who_is_it_for))]);
        $request ->request->add(['requirements' => json_encode(explode(',', $request->requirements))]);

        $course -> update($request->all());

        return response()->json(['course'=> CourseResource::make($course)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        $course = Course::findOrFail($id);
        $course->delete();
        return response()->json(['message' => 200]);
    }
}
