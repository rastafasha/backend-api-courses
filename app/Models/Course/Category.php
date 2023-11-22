<?php

namespace App\Models\Course;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Discount\DiscountCategorie;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable =[
        'nombre',
        'imagen',
        'state',
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
    public function children(){
        return $this->hasMany(Category::class, "category_id");
    }
    public function father(){
        return $this->belongsTo(Category::class, "category_id");
        
    }
    public function courses(){
        return $this->hasMany(Course::class);
        
    }
    public function discount_categories(){
        return $this->hasMany(DiscountCategorie::class);
        
    }

    // buscar

    public function scopeFilterAdvance($query, $search, $state)
    {
        if($search){
            $query->where('nombre', 'like', '%'.$search.'%');
        }
        if($state){
            $query->where('state', $state);
        }
        return $query;
    }
}
