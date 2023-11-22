<?php

namespace App\Http\Resources\Curso;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
                'title'=>$this->resource-> title,
                'subtitle'=>$this->resource-> subtitle,
                'slug'=>$this->resource-> slug,
                'price_usd'=>$this->resource-> price_usd,
                'price_bves'=>$this->resource-> price_bves,
                // "imagen"=> $this->resource->imagen ? env("APP_URL")."storage/".$this->resource->imagen : null,
                "imagen"=> $this->resource->imagen ? env("APP_URL").$this->resource->imagen : null,
                'category_id'=>$this->resource-> category_id,
                'category'=>[
                    'id'=> $this->resource->category->id,
                    'name'=> $this->resource->category->nombre
                ],
                'sub_category_id'=>$this->resource->sub_category_id,
                'sub_category'=>[
                    'id'=> $this->resource->sub_category->id,
                    'name'=> $this->resource->sub_category->nombre
                ],
                'user_id'=>$this->resource-> user_id,
                'user'=>[
                    'id'=>$this->resource->instructor->id,
                    'full_name'=>$this->resource->instructor->name.' '.$this->resource->instructor->surname,
                    'email'=>$this->resource->instructor->email,
                ],
                'level'=>$this->resource-> level,
                'idioma'=>$this->resource-> idioma,
                'vimeo_id'=>$this->resource-> vimeo_id,
                'time'=>$this->resource-> time,
                // 'time_course'=>$this->resource-> time_course,
                'description'=>$this->resource-> description,
                'who_is_it_for'=>json_decode($this->resource-> who_is_it_for),
                'requirements'=> json_decode($this->resource-> requirements),
                'state'=>$this->resource-> state,
                // "created_at"=>$this->resource->created_at->format("Y-m-d h:i:s"),
                // "updated_at"=>$this->resource->updated_at->format("Y-m-d h:i:s"),
                // "deleted_at"=>$this->resource->deleted_at->format("Y-m-d h:i:s"),
        ];
    }
}
