<?php

namespace App\Http\Resources\Ecommerce\LandingCurso;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingCursoResource extends JsonResource
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
            'files_count'=>$this->resource-> files_count,
            'count_students'=>$this->resource-> count_students,
            'avg_reviews'=>$this->resource-> avg_reviews ? round($this->resource-> avg_reviews,2): 0,
            'count_reviews'=>$this->resource-> count_reviews,
            'time_course'=>$this->resource-> time_course,
            'idioma'=>$this->resource-> idioma,
            'level'=>$this->resource-> level,
            'discount_g'=>$discount_g,
            'discount_date'=>$discount_g ? Carbon::parse($discount_g->end_date,)->format("Y-m-d"):NULL,
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
            'level'=>$this->resource-> level,
            'idioma'=>$this->resource-> idioma,
            'time'=>$this->resource-> time,
            'vimeo_id'=>$this->resource-> vimeo_id ? "https://player.vimeo.com/video/".$this->resource-> vimeo_id : NULL,
            'description'=>$this->resource-> description,
                'who_is_it_for'=>json_decode($this->resource-> who_is_it_for),
                'requirements'=> json_decode($this->resource-> requirements),
            // "imagen"=> $this->resource->imagen ? env("APP_URL")."storage/".$this->resource->imagen : null,
            "imagen"=> $this->resource->imagen ? env("APP_URL").$this->resource->imagen : null,
            'instructor'=>$this->resource-> instructor ? [
                "id"=>$this->resource->instructor->id,
                "profesion"=>$this->resource->instructor->profesion,
                "courses_count"=>$this->resource->instructor->courses_count,
                "description"=>$this->resource->instructor->description,
                "full_name"=>$this->resource->instructor->name.' '.$this->resource->instructor->surname,
                "avatar"=>env("APP_URL").$this->resource->instructor->avatar,
                // "avatar"=>env("APP_URL")."storage/".$this->resource->instructor->avatar,
                "avg_reviews"=>round($this->resource->instructor->avg_reviews, 2),
                "count_reviews"=>$this->resource->instructor->count_reviews,
                "count_students"=>$this->resource->instructor->count_students,

            ] : NULL,
                //malla curricular
                "malla"=>$this->resource->sections->map(function($section){
                    return[
                        "id"=>$section->id,
                        "name"=>$section->name,
                        "time_section"=>$section->time_section,
                        "clases"=>$section->classes->map(function($classe){
                            return[
                                "id"=>$classe->id,
                                "name"=>$classe->name,
                                "time_classe"=>$classe->time_classe,
                                "vimeo"=>$classe->vimeo_id ? "https://player.vimeo.com/video/".$this->resource-> vimeo_id : NULL,
                                "file"=>$classe->files->map(function($file){
                                    return [
                                        "name"=> $file->name_file,
                                        "url"=> env("APP_URL")."storage/".$file->file,
                                        "size"=>$file->size,

                                    ];
                                })
                            ];
                        })
                    ];
                }),
                "updated_at"=>$this->resource->updated_at->format("m/Y"),
                "reviews"=>$this->resource->reviews->map(function($review){
                    return[
                        "message"=>$review->message,
                        "rating"=>$review->rating,
                        "user"=>[
                            "full_name"=>$review->user->name.''.$review->user->surname,
                            // "avatar"=> env("APP_URL")."storage/".$review->user->avatar
                            "avatar"=> env("APP_URL").$review->user->avatar
                        ]
                    ];
                })

        ];
    }
}
