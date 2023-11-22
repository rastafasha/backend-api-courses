<?php

namespace App\Http\Controllers\tienda;

use App\Models\User;
use App\Models\Sale\Sale;
use Illuminate\Http\Request;
use App\Models\CoursesStudent;
use App\Models\Sale\SaleDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Ecommerce\Sale\SaleCollection;
use App\Http\Resources\Ecommerce\Course\CourseHomeResource;

class ProfileClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function profile(Request $request)
    {
        $user = auth('api')->user();
        //count courses
        $enrolledCourseCount = CoursesStudent::where("user_id", $user->id)->count();
        $activeCourseCount = CoursesStudent::where("user_id", $user->id)->where("clases_checkeds", "<>", NULL)->count();
        $completedCourseCount = CoursesStudent::where("user_id", $user->id)->where("state", 2)->count();
        //enroled courses
        $enrolledCourses = CoursesStudent::where("user_id", $user->id)->get();
        $activeCourses = CoursesStudent::where("user_id", $user->id)->where("clases_checkeds", "<>", NULL)->get();
        $completedCourses = CoursesStudent::where("user_id", $user->id)->where("state", 2)->get();

        //reviews
        $sale_details = SaleDetail::whereHas("sale", function($q) use($user){
            $q->where("user_id",$user->id);
        })->orderBy("id", "desc")->get();
        
        //sales history
        $sales = Sale::where("user_id", $user->id)->orderBy("id", "desc")->get();




        return response()->json([
            "user"=>[
                "name"=>$user->name,
                "surname"=>$user->surname ?? '',
                "email"=>$user->email,
                "role"=>$user->role,
                "phone"=>$user->phone,
                "description"=>$user->description,
                "profesion"=>$user->profesion,
                "avatar"=> $user->avatar ? env("APP_URL")."storage/".$user->avatar : null,
            ],
            "enrolledCourseCount"=> $enrolledCourseCount,
            "activeCourseCount"=> $activeCourseCount,
            "completedCourseCount"=> $completedCourseCount,
            "enrolledCourses"=>$enrolledCourses->map(function($course_student){
                $clases_checkeds = $course_student->clases_checkeds ? explode(",",$course_student->clases_checkeds) : [];
                return[
                    "id"=>$course_student->id,
                    "clases_checkeds"=>$clases_checkeds,
                    // "porcentage"=>round((sizeof($clases_checkeds)/$course_student->course->count_class )*100,2),
                    "percentage" => $course_student->course->count_class > 0 ? round((sizeof($clases_checkeds)/$course_student->course->count_class)*100,2) : 0,
                    "course"=>CourseHomeResource::make($course_student->course),
                ];
            }),
            "activeCourses"=>$activeCourses->map(function($course_student){
                $clases_checkeds = $course_student->clases_checkeds ? explode(",",$course_student->clases_checkeds) : [];
                return[
                    "id"=>$course_student->id,
                    "clases_checkeds"=>$clases_checkeds,
                    "porcentage"=>round((sizeof($clases_checkeds)/$course_student->course->count_class )*100,2),
                    "course"=>CourseHomeResource::make($course_student->course),
                ];
            }),
            "completedCourses"=>$completedCourses->map(function($course_student){
                $clases_checkeds = $course_student->clases_checkeds ? explode(",",$course_student->clases_checkeds) : [];
                return[
                    "id"=>$course_student->id,
                    "clases_checkeds"=>$clases_checkeds,
                    "porcentage"=>round((sizeof($clases_checkeds)/$course_student->course->count_class )*100,2),
                    "course"=>CourseHomeResource::make($course_student->course),
                ];
            }),
            "saleDetails" => $sale_details->map(function($sale_detail){
                return [
                    "id"=> $sale_detail->id,
                    "review"=> $sale_detail->review,
                    "course"=> [
                        "id"=>$sale_detail->course->id,
                        "title"=>$sale_detail->course->title,
                        "imagen"=>env("APP_URL")."storage/".$sale_detail->course->imagen,
                    ],
                    "created_at"=>$sale_detail->created_at->format("Y-m-d h:i:s")
                ];
            }),
            "sales"=>SaleCollection::make($sales),

            ]);
    }

    public function updateclient(Request $request)
    {
        $user = User::findOrFail(auth('api')->user()->id);
        if($request->new_password){
            $request->request->add(["password" => Hash::make($request->new_password)]);
        }

        if($request->hasFile("imagen")){
            if($user->avatar){
                Storage::delete();
            }
            $path = Storage::putFile("users",$request->file("imagen"));
            $request->request->add(["avatar"=>$path]);
        }
        $user ->update($request->all());

        return response()->json([
            "message"=> 200
        ]);


    }

    
}
