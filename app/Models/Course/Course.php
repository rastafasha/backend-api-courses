<?php

namespace App\Models\Course;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Sale\Review;
use App\Models\CoursesStudent;
use App\Models\Course\Category;
use App\Models\Course\CourseSection;
use App\Models\Discount\DiscountCourse;
use Illuminate\Database\Eloquent\Model;
use App\Models\Discount\DiscountCategorie;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable =[
        'title',
        'subtitle',
        'slug',
        'imagen',
        'price_usd',
        'price_bves',
        'user_id',
        'category_id',
        'sub_category_id',
        'level',
        'idioma',
        'vimeo_id',
        'time',
        'description',
        'requirements',
        'who_is_it_for',
        'state',

    ];

    public function setCreateAttribute($value){
        date_default_timezone_set("America/Caracas"); 
        $this->attribute['created_at']= Carbon::now();
    }

    public function setUpdateAttribute($value){
        date_default_timezone_set("America/Caracas"); 
        $this->attribute['updated_at']= Carbon::now();
    }

    
    // relaciones

    public function instructor(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function sub_category(){
        return $this->belongsTo(Category::class);
    }

    public function sections(){
        return $this->hasMany(CourseSection::class);
    }
    public function discount_courses(){
        return $this->hasMany(DiscountCourse::class);
    }

    public function discount_categories(){
        return $this->hasMany(DiscountCategorie::class);
    }

    public function course_students(){
        return $this->hasMany(CoursesStudent::class);
    }

    public function reviews(){
        return $this->hasMany(Review::class);
    }


    // buscar admin

    public function scopeFilterAdvance($query, $search, $state)
    {
        if($search){
            $query->where('title', 'like', '%'.$search.'%');
        }
        if($state){
            $query->where('state', $state);
        }
        return $query;
    }


    //buscar tienda

    public function scopeFilterAdvanceEcommerce($query, $search, 
    $selectedCategories =[], $instructoresSelected = [],
    $min_price = 0, $max_price = 0,
    $levelsSelected = [], $idiomasSelected = [],
    $courses_a = [], $ratingSelected = 0
    )
    {
        if($search){
            $query->where('title', 'like', '%'.$search.'%');
        }
        if(sizeof($selectedCategories) > 0){
            $query->whereIn('category_id',$selectedCategories);
        }
        if(sizeof($instructoresSelected) > 0){
            $query->whereIn('user_id',$instructoresSelected);
        }
        if($min_price > 0 && $max_price > 0 ){
            $query->whereBetween('price_usd', [$min_price, $max_price]);
        }
        if(sizeof($levelsSelected) > 0){
            $query->whereIn('level',$levelsSelected);
        }
        if(sizeof($idiomasSelected) > 0){
            $query->whereIn('idioma',$idiomasSelected);
        }
        if($courses_a || $ratingSelected ){
            $query->whereIn('id',$courses_a);
        }
        return $query;
    }

    public function getDiscountCAttribute(){
        date_default_timezone_set("America/Caracas");
        $discount = null;
        foreach ($this->discount_courses as $key => $discount_course) {
            if($discount_course->discount->type_campaing == 1 && $discount_course->discount->state == 1){
                if(Carbon::now()->between($discount_course->discount->start_date, Carbon::parse($discount_course->discount->end_date)->addDays(1))){
                    //existe una campaña de descuento con el curso
                    $discount = $discount_course->discount;
                    break;
                }
            }
        }
        return $discount;
    }
    public function getDiscountCTAttribute(){
        date_default_timezone_set("America/Caracas");
        $discount = null;
        foreach ($this->category->discount_categories as $key => $discount_categorie) {
            if($discount_categorie->discount->type_campaing == 1 && $discount_categorie->discount->state == 1){
                if(Carbon::now()->between($discount_categorie->discount->start_date, Carbon::parse($discount_categorie->discount->end_date)->addDays(1))){
                    //existe una campaña de descuento con el curso
                    $discount = $discount_categorie->discount;
                    break;
                }
            }
        }
        return $discount;
    }

    //agregar el tiempo
    function AddTimes($horas)
        {
            $total = 0;
            foreach($horas as $h) {
                $parts = explode(":", $h);
                $total += $parts[2] + $parts[1]*60 + $parts[0]*3600;
            }
            $hours = floor($total / 3600);
            $minutes = floor(($total / 60) % 60);
            $seconds = $total % 60;

            return $hours." hrs ".$minutes." mins";
    }

    public function getFilesCountAttribute(){

        $files_count = 0;

        foreach ($this->sections as $keyS => $section) {
            foreach ($section->classes as $keyC => $classe) {
                $files_count += $classe->files->count();
            } 
        }

        return $files_count;
    }

    //agregar el count
    public function getCountClassAttribute(){
        $num = 0;
        
        foreach($this->sections as $key => $section){
            $num +=$section->classes->count();
        }

        return $num;
    }
    


    public function getTimeCourseAttribute(){
        $times = [];
        foreach($this->sections as $keyS => $section){
            foreach($section->classes as $keyC => $classe){
                array_push($times,$classe->time);
            }
        }

        return $this->AddTimes($times);
        
    }

    public function getTimeClassAttribute(){
        $times = [];
        foreach($this->sections as $keyS => $section){
            foreach($section->classes as $keyC => $classe){
                array_push($times,$classe->time);
            }
        }

        return $this->AddTimes($times);
        
    }
    //fin-agregar el tiempo

    public function getCountStudentsAttribute(){
        
        return $this->course_students->count();
    }
    
    public function getCountReviewsAttribute(){
        
        return $this->reviews->count();
    }
    
    public function getAvgReviewsAttribute(){
        
        return $this->reviews->avg("rating");
    }
    



}
