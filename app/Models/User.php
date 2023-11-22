<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Course\Course;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'surname',
        'avatar',
        'profesion',
        'description',
        'phone',
        'role_id',
        'isInstructor',
        'state',//1 activo 2 inactivo (acceso al sistema)
        'type_user'//1 es tipo cliente 2 tipo admin
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
 
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    //relaciones
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class)->where("state",2);
    }


    //
    public function getCoursesCountAttribute()
    {
        return $this->courses->count();
    }

    public function getAvgReviewsAttribute()
    {
        return $this->courses->avg("avg_reviews");
    }

    public function getCountReviewsAttribute()
    {
        return $this->courses->sum("count_reviews");
    }

    public function getCountStudentsAttribute()
    {
        return $this->courses->sum("count_students");
    }



    public function scopeFilterAdvance($query, $search, $state)
    {
        if($search){
            $query->where('email', 'like', '%'.$search.'%');
        }
        if($state){
            $query->where('state', $state);
        }
        return $query;
    }
}
