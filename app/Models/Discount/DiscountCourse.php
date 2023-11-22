<?php

namespace App\Models\Discount;

use Carbon\Carbon;
use App\Models\Course\Course;
use App\Models\Discount\Discount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountCourse extends Model
{
    use HasFactory;
    protected $fillable =[
        'discount_id',
        'course_id',

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

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function discount(){
        return $this->belongsTo(Discount::class);
    }
}
