<?php

namespace App\Models\Sale;

use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "sale_id",
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

    public function course(){
        return $this->belongsTo(Course::class);

    }

    public function sale(){
        return $this->belongsTo(Sale::class);

    }

    public function review(){
        return $this->hasOne(Review::class);

    }
}
