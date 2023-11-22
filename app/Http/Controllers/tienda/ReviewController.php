<?php

namespace App\Http\Controllers\tienda;

use App\Models\Sale\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

        $request->request->add(["user_id"=>auth('api')->user()->id]);
        $review = Review::create($request->all());

        return response()->json([
            "review"=>$review
        ]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $coupon = Coupon::findOrFail($id);
        return response()->json([
            'coupon'=>CouponClaseResource::make($coupon)
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
        $review = Review::findOrFail($id);
        $review ->update($request->all());
        return response()->json(["message"=>200]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::findOrFail($id);
        $review->delete();
        return response()->json(["message"=>200]);
    }
}
