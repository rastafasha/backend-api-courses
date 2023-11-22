<?php

namespace App\Models\Course;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseSection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable =[
        'course_id',
        'name',
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

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function classes(){
        return $this->hasMany(CourseClasse::class, "course_section_id");
    }

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

    public function getTimeSectionAttribute(){
        $times = [];
        foreach($this->classes as $keyC => $classe){
            array_push($times,$classe->time);
        }
        return $this->AddTimes($times);
        
    }
}
