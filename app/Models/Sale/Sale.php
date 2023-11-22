<?php

namespace App\Models\Sale;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Sale\SaleDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "user_id",
        "method_payment",
        "currency_total",
        "currency_payment",
        "total",
        "price_dolar",
        "n_transaccion"
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
        return $this->belongsTo(User::class, "user_id");
    }

    public function sale_details(){
        return $this->hasMany(SaleDetail::class);
    }
}
