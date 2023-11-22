<?php

namespace App\Http\Resources\Discount;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id'=>$this->resource-> id,
            'code'=>$this->resource->code,
            'state'=>$this->resource->state ?? 1, 
            'type_discount'=>$this->resource->type_discount,//porcentaje o entero
            'discount_type'=>$this->resource->discount_type,
            'discount'=>$this->resource->discount,//monto del descuento
            'start_date'=>Carbon::parse($this->resource->start_date)->format("Y-m-d"),//1 es ilimitado 2 limitado 
            'end_date'=>Carbon::parse($this->resource->end_date)->format("Y-m-d"),//numero de usos permitidos
            'type_campaing'=>$this->resource->type_campaing,
            
            'courses'=>$this->resource->courses->map(function($course_aux){
                return[
                    'id'=>$course_aux->course?->id,
                    'title'=>$course_aux->course?->title,
                    // 'imagen'=>env("APP_URL")."storage/".$course_aux->course->imagen,
                    'imagen'=>env("APP_URL").$course_aux->course->imagen,
                    'aux_id'=>$course_aux->id
                ];
            }),

            'categories'=> $this->resource->categories->map(function($categorie_aux){
                return[
                    'id'=>$categorie_aux->category?->id,
                    'nombre'=>$categorie_aux->category?->nombre,
                    // 'imagen'=>env("APP_URL")."storage/".$categorie_aux->category?->imagen,
                    'imagen'=>env("APP_URL").$categorie_aux->category?->imagen,
                    'aux_id'=>$categorie_aux->id
                ];
            }),
            
        ];
    }
}
