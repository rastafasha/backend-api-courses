<?php

namespace App\Models\Coupon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'code',
        'type_coupon',//categoria o curso
        'type_discount',//porcentaje o entero
        'discount',//monto del descuento
        'type_count',//1 es ilimitado 2 limitado 
        'num_use',//numero de usos permitidos
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
        return $this->hasMany(CouponCourse::class);
    }
    public function categories(){
        return $this->hasMany(CouponCategorie::class);
    }

    // buscar

    public function scopeFilterAdvance($query, $search, $state)
    {
        if($search){
            $query->where('code', 'like', '%'.$search.'%');
        }
        if($state){
            $query->where('state', $state);
        }
        return $query;
    }

}
