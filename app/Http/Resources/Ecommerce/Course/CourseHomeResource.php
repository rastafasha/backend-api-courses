<?php

namespace App\Http\Resources\Ecommerce\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseHomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //es la campaña de descuento con la que sta relacionada
        $discount_g = null;

        // if(){//curso tiene descuento a nivel de (curso y a nivel de categoria)
            // $discount_g = Al descuento tipo categoria
        // }else{
            // if(){ //curso tiene descuento a nivel de curso y no de nivel de categoria
                //$discount_g = //al descuento de tipo curso
            // }else{
                // if(){// curso no tiene descuento a nivel de curso pero si tiene a nivel de categoria
                    //$discount_g = //al descuento tipo categoria
                // }
            // }
        // }

        if($this->resource->discount_c && $this->resource->discount_c_t){
            $discount_g = $this->resource->discount_c_t;
        }else{
            if($this->resource->discount_c && !$this->resource->discount_c_t){
                $discount_g = $this->resource->discount_c;
            }else{
                if(!$this->resource->discount_c && $this->resource->discount_c_t){
                    $discount_g = $this->resource->discount_c_t;
                }
            }
        }

        return[
                
            'id'=>$this->resource-> id,
            'title'=>$this->resource-> title,
            'subtitle'=>$this->resource-> subtitle,
            'slug'=>$this->resource-> slug,
            'price_usd'=>$this->resource-> price_usd,
            'price_bves'=>$this->resource-> price_bves,
            'count_class'=>$this->resource-> count_class,
            // 'time'=>$this->resource-> time,
            'time_course'=>$this->resource-> time_course,
            'idioma'=>$this->resource-> idioma,
            'level'=>$this->resource-> level,
            'discount_g'=>$discount_g,
            'count_students'=>$this->resource-> count_students,
            'avg_reviews'=>$this->resource-> avg_reviews ? round($this->resource-> avg_reviews,2): 0,
            'count_reviews'=>$this->resource-> count_reviews,
            // "imagen"=> $this->resource->imagen ? env("APP_URL")."storage/".$this->resource->imagen : null,
            "imagen"=> $this->resource->imagen ? env("APP_URL").$this->resource->imagen : null,
            'instructor'=>$this->resource-> instructor ? [
                "id"=>$this->resource->instructor->id,
                "profesion"=>$this->resource->instructor->profesion,
                "full_name"=>$this->resource->instructor->name.' '.$this->resource->instructor->surname,
                // "avatar"=>env("APP_URL")."storage/".$this->resource->instructor->avatar,
                "avatar"=>env("APP_URL").$this->resource->instructor->avatar,

            ] : NULL
    ];
    }
}
