<?php

namespace App\Models\Discount;

use Carbon\Carbon;
use App\Models\Course\Category;
use App\Models\Discount\Discount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountCategorie extends Model
{
    use HasFactory;
    protected $fillable =[
        'discount_id',
        'category_id',

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

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function discount(){
        return $this->belongsTo(Discount::class);
    }
}
