<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Admin extends Authenticatable implements JWTSubject
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'admins';
    protected $primaryKey='id';
    protected $fillable = [
        'email', 'password', 'image', 'name'
    ];

    ########### relations ##############
    public function admin_notification(){
        return $this->hasMany('App\Models\Admin_notifiation');
    }

    public function fashion_news(){
        return $this->hasMany('App\Models\Fashion_news', 'type_id', 'id');
    }

    public function reply(){
        return $this->hasMany('App\Models\Reply');
    }

    public function comment(){
        return $this->hasMany('App\Models\Comment');
    }

    ####################### end relations ##############################

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
