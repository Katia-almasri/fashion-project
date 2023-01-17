<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Expert extends Authenticatable implements JWTSubject
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'experts';
    protected $primaryKey='id';
    protected $fillable = [
        'name', 'email', 'password', 'date_of_birth', 'gender', 'details', 'image'
    ];
    protected $dates = ['deleted_at'];


    ########### relations ##############
    public function expert_notification(){
        return $this->hasMany('App\Models\Expert_notification', 'expert_id', 'id');
    }

    public function comment(){
        return $this->hasMany('App\Models\Comment', 'expert_id', 'id');
    }

    public function fashion_news(){
        return $this->hasMany('App\Models\Fashion_news', 'type_id', 'id');
    }

    public function msg(){
        return $this->hasMany('App\Models\Msg', 'expert_id', 'id');
    }

    ####################### end relations ####################
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
}
