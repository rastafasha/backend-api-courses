<?php

namespace App\Http\Controllers\tienda;

use App\Models\Sale\Cart;
use Illuminate\Http\Request;
use App\Models\Coupon\Coupon;
use App\Models\CoursesStudent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ecommerce\Cart\CartResource;
use App\Http\Resources\Ecommerce\Cart\CartCollection;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth('api')->user();
        $carts = Cart::where("user_id", $user->id)->get();

        return response()->json([
            "carts" => CartCollection::make($carts)
        ]);
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();
        //si el curso ya pertenece al student
        $isHaveCourse = CoursesStudent::where("user_id", $user->id)->where("course_id",$request->course_id)->first();
        //si el curso ya existe en el carrito
        $isExistCart = Cart::where("user_id", $user->id)->where("course_id",$request->course_id)->first();
        if($isHaveCourse){
            return response()->json([
                "message"=>403,
                "message_text"=>"Ya has adiquirido este curso"
            ]);
        }
        if($isExistCart){
            return response()->json([
                "message"=>403,
                "message_text"=>"El curso ya en la lista"
            ]);
        }

        $request->request->add(["user_id"=> $user->id]);

        $cart = Cart::create($request->all());

        return response()->json([
            "cart" => CartResource::make($cart)
        ]);

    }

    /**
     * Show the form for aplication of coupons.
     */
    public function apply_coupon(Request $request)
    {
        $cupon = Coupon::where("code", $request->code)->where("state", 1)->first();

        if(!$cupon){
            return response()->json([
                "message"=>403,
                "message_text"=> "El Cupón ingresado no existe."
            ]);
        }

        $carts = Cart::where("user_id", auth('api')->user()->id)->get();

        foreach ($carts as $key => $cart) {
            if($cupon->type_coupon == 1){ //cupon es de tipo curso
                $isExistCourseCoupon = false;
                foreach ($cupon->courses as $key => $course) {
                    if($course->course_id == $cart->course_id){
                        $isExistCourseCoupon = true;
                        break;
                    }
                }
                if($isExistCourseCoupon){
                    $total = 0;

                    if($cupon->type_discount == 1){//%
                        $total = $cart->precio_unitario -$cart->precio_unitario*($cupon->discount*0.01);
                    }
                    if($cupon->type_discount == 2){//usd
                        $total = $cart->precio_unitario -$cupon->discount;

                    }
                    $cart->update([
                        "type_discount" =>$cupon->type_discount,
                        "discount" =>$cupon->discount,
                        "type_campaing" => NULL,
                        "code_cupon" =>$cupon->code,
                        "code_discount" =>NUll,
                        "total" =>$total,
                    ]);
                }
            }
            if($cupon->type_coupon == 2){//cupon de tipo category
                $isExistCourseCoupon = false;
                foreach ($cupon->categories as $key => $category) {
                    if($course->category_id == $cart->course->category){
                        $isExistCourseCoupon = true;
                        break;
                    }
                }
                if($isExistCourseCoupon){
                    $total = 0;

                    if($cupon->type_discount == 1){//%
                        $total = $cart->precio_unitario -$cart->precio_unitario*($cupon->discount*0.01);
                    }
                    if($cupon->type_discount == 2){//usd
                        $total = $cart->precio_unitario -$cupon->discount;

                    }
                    $cart->update([
                        "type_discount" =>$cupon->type_discount,
                        "discount" =>$cupon->discount,
                        "type_campaing" => NULL,
                        "code_cupon" =>$cupon->code,
                        "code_discount" =>NUll,
                        "total" =>$total,
                    ]);
                }
            }

            $carts = Cart::where("user_id", auth('api')->user()->id)->get();

            return response()->json([
                "carts" => CartCollection::make($carts)
            ]);
        }

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();

        return response()->json([
            "message"=>200
        ]);
    }
}
