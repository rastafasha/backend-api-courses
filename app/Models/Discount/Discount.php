<?php

namespace App\Models\Discount;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Discount\DiscountCourse;
use App\Models\Discount\DiscountCategorie;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'code',
        'type_discount',//porcentaje o entero
        'discount',//monto del descuento
        'discount_type',//categoria o curso
        'start_date',
        'end_date',
        'type_campaing',
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

    public function courses(){
        return $this->hasMany(DiscountCourse::class);
    }
    public function categories(){
        return $this->hasMany(DiscountCategorie::class);
    }

    // buscar
    // $search,
    public function scopeFilterAdvance($query,  $state)
    {
        // if($search){
        //     $query->where('code', 'like', '%'.$search.'%');
        // }
        if($state){
            $query->where('state', $state);
        }
        return $query;
    }
}
