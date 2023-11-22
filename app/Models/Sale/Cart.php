<?php

namespace App\Models\Sale;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id",
        "course_id",
        "type_discount",
        "discount",
        "type_campaing",
        "code_discount",
        "code_cupon",
        "precio_unitario",
        "total",

    ];

    public function setCreateAttribute($value){
        date_default_timezone_set("America/Caracas"); 
        $this->attribute['created_at']= Carbon::now();
    }

    public function setUpdateAttribute($value){
        date_default_timezone_set("America/Caracas"); 
        $this->attribute['updated_at']= Carbon::now();
    }

    public function user(){
        return $this->belongsTo(User::class);

    }
    public function course(){
        return $this->belongsTo(Course::class);

    }
}
