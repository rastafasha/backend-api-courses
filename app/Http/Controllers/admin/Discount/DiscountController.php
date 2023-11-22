<?php

namespace App\Http\Controllers\admin\Discount;

use Illuminate\Http\Request;
use App\Models\Discount\Discount;
use App\Http\Controllers\Controller;
use App\Models\Discount\DiscountCourse;
use App\Models\Discount\DiscountCategorie;
use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\Discount\DiscountCollection;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $state = $request->state;
        
        $discounts = Discount::filterAdvance($search, $state)->orderBy("id", "desc")->get();

        return response()->json([
            "message" => 200,
            // "coupons" => $coupons
            "discounts" => DiscountCollection::make($discounts)
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
        //discount_type 1 curso 2 categorie
        //validacion a nivel de curso
        if($request->discount_type == 1){
            foreach($request->course_selected as $key => $course){
                $IS_DISCOUNT_START_DATE = Discount::where("type_campaing", $request->type_campaing)
                ->where("discount_type", $request->discount_type)
                ->whereHas("courses", function($q) use($course){
                    return $q->where("course_id",$course["id"]);
                })->whereBetween("start_date",[$request->start_date,$request->end_date])->first();

                $IS_DISCOUNT_END_DATE = Discount::where("type_campaing", $request->type_campaing)
                ->where("discount_type", $request->discount_type)
                ->whereHas("courses", function($q) use($course){
                    return $q->where("course_id",$course["id"]);
                })->whereBetween("start_date",[$request->start_date,$request->end_date])->first();


                if($IS_DISCOUNT_START_DATE || $IS_DISCOUNT_END_DATE ){
                    return response()->json([
                        "message"=> 403, 
                        "message_text"=>"El Curso".$course["title"]."ya se encuentra en una campaña de descuento",($IS_DISCOUNT_START_DATE ? $IS_DISCOUNT_START_DATE->id : ''), ($IS_DISCOUNT_END_DATE ? $IS_DISCOUNT_END_DATE->id : '')
                    ]);
                }
            }
        }
        //validacion a nivel de categoria
        if($request->discount_type == 2){
            foreach($request->category_selected as $key => $categorie){
                $IS_DISCOUNT_START_DATE = Discount::where("type_campaing", $request->type_campaing)
                ->where("discount_type", $request->discount_type)
                ->whereHas("categories", function($q) use($categorie){
                    return $q->where("category_id",$categorie["id"]);
                })->whereBetween("start_date",[$request->start_date,$request->end_date])->first();

                $IS_DISCOUNT_END_DATE = Discount::where("type_campaing", $request->type_campaing)
                ->where("discount_type", $request->discount_type)
                ->whereHas("categories", function($q) use($categorie){
                    return $q->where("category_id",$categorie["id"]);
                })->whereBetween("start_date",[$request->start_date,$request->end_date])->first();


                if($IS_DISCOUNT_START_DATE || $IS_DISCOUNT_END_DATE ){
                    return response()->json([
                        "message"=> 403, 
                        "message_text"=>"La Categoría".$categorie["nombre"]."ya se encuentra en una campaña de descuento",($IS_DISCOUNT_START_DATE ? $IS_DISCOUNT_START_DATE->id : ''), ($IS_DISCOUNT_END_DATE ? $IS_DISCOUNT_END_DATE->id : '')
                    ]);
                }
            }
        }

        
        $request->request->add(["code"=> uniqid() ]);

        $discount = Discount::create($request->all());

        if($request->discount_type == 1){
            
            foreach($request->course_selected as $key => $course){
                DiscountCourse::create([
                    "discount_id"=> $discount->id,
                    "course_id"=> $course["id"],
                ]);
            }
        }   
        if($request->discount_type == 2){
            foreach($request->category_selected as $key => $categorie){
                DiscountCategorie::create([
                    "discount_id"=> $discount->id,
                    "category_id"=> $categorie["id"],
                ]);
            }
        }

        return response()->json(["message"=>200]);
        
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $discount = Discount::findOrFail($id);

        return response()->json([
            "discount"=>DiscountResource::make($discount)
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
       //discount_type 1 curso 2 categorie
        //validacion a nivel de curso
        if($request->discount_type == 1){
            foreach($request->course_selected as $key => $course){
                $IS_DISCOUNT_START_DATE = Discount::where("type_campaing", $request->type_campaing)
                ->where("id","<>",$id)->where("discount_type", $request->discount_type)
                ->whereHas("courses", function($q) use($course){
                    return $q->where("course_id",$course["id"]);
                })->whereBetween("start_date",[$request->start_date,$request->end_date])->first();

                $IS_DISCOUNT_END_DATE = Discount::where("type_campaing", $request->type_campaing)
                ->where("id","<>",$id)->where("discount_type", $request->discount_type)
                ->whereHas("courses", function($q) use($course){
                    return $q->where("course_id",$course["id"]);
                })->whereBetween("start_date",[$request->start_date,$request->end_date])->first();


                if($IS_DISCOUNT_START_DATE || $IS_DISCOUNT_END_DATE ){
                    return response()->json([
                        "message"=> 403, 
                        "message_text"=>"El Curso".$course["title"]."ya se encuentra en una campaña de descuento",($IS_DISCOUNT_START_DATE ? $IS_DISCOUNT_START_DATE->id : ''), ($IS_DISCOUNT_END_DATE ? $IS_DISCOUNT_END_DATE->id : '')
                    ]);
                }
            }
        }
        //validacion a nivel de categoria
        if($request->discount_type == 2){
            foreach($request->category_selected as $key => $categorie){
                $IS_DISCOUNT_START_DATE = Discount::where("type_campaing", $request->type_campaing)
                ->where("id","<>",$id)->where("discount_type", $request->discount_type)
                ->whereHas("categories", function($q) use($categorie){
                    return $q->where("category_id",$categorie["id"]);
                })->whereBetween("start_date",[$request->start_date,$request->end_date])->first();

                $IS_DISCOUNT_END_DATE = Discount::where("type_campaing", $request->type_campaing)
                ->where("id","<>",$id)->where("discount_type", $request->discount_type)
                ->whereHas("categories", function($q) use($categorie){
                    return $q->where("category_id",$categorie["id"]);
                })->whereBetween("start_date",[$request->start_date,$request->end_date])->first();


                if($IS_DISCOUNT_START_DATE || $IS_DISCOUNT_END_DATE ){
                    return response()->json([
                        "message"=> 403, 
                        "message_text"=>"La Categoría".$categorie["nombre"]."ya se encuentra en una campaña de descuento",($IS_DISCOUNT_START_DATE ? $IS_DISCOUNT_START_DATE->id : ''), ($IS_DISCOUNT_END_DATE ? $IS_DISCOUNT_END_DATE->id : '')
                    ]);
                }
            }
        }

        // $request->request->add(["code"=> uniqid() ]);

        $discount = Discount::findOrFail($id);
        $discount ->update($request->all());

        foreach($discount->categories as $key => $categorieD){
            $categorieD->delete();
        }

        foreach($discount->courses as $key => $courseD){
            $courseD->delete();
        }

        if($request->discount_type == 1){
            
            foreach($request->course_selected as $key => $course){
                DiscountCourse::create([
                    "discount_id"=> $discount->id,
                    "course_id"=> $course["id"],
                ]);
            }
        }   
        if($request->discount_type == 2){
            foreach($request->category_selected as $key => $categorie){
                DiscountCategorie::create([
                    "discount_id"=> $discount->id,
                    "category_id"=> $categorie["id"],
                ]);
            }
        }

        return response()->json(["message"=>200]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return response()->json(["message"=>200]);
    }
}
