<?php

namespace App\Http\Controllers\tienda;

use App\Mail\SaleMail;
use App\Models\Sale\Cart;
use App\Models\Sale\Sale;
use Illuminate\Http\Request;
use App\Models\CoursesStudent;
use App\Models\Sale\SaleDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
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

        $sale = Sale::create($request->all());

        $carts = Cart::where("user_id", auth('api')->user()->id)->get();

        foreach ($carts as $key => $cart) {
            $newDetail = [];
            $newDetail = $cart->toArray();
            $newDetail["sale_id"] = $sale->id;
            SaleDetail::create($newDetail);
            CoursesStudent::create([
                "course_id" => $newDetail["course_id"],
                "user_id" => auth('api')->user()->id,
            ]);
            $cart->delete();
        }
        Mail::to($sale->user->email)->send(new SaleMail($sale));
        return response()->json([
            "message"=>200,
            "message_text"=>"Los cursos se han adquirido correctamente."
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
