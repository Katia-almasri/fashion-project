<?php

namespace App\Models;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; 

class Table_user extends Authenticatable implements JWTSubject
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'table_users';
    protected $primaryKey='id';
    protected $fillable = [
        'name', 'email', 'password', 'date_of_birth', 'gender', 'details', 'weight', 'length', 'prefered_color', 'prefered_style', 'image'
    ];

    ########### Begin Relations ######################
    public function question(){
        return $this->hasMany('App\Models\Question');
    }

    public function user_notification(){
        return $this->hasMany('App\Models\User_notification', 'user_id', 'id');
    }

    public function comment(){
        return $this->hasMany('App\Models\Comment', 'user_id', 'id');
    }

    public function follow(){
        return $this->hasMany('App\Models\Follow','user_id','id');
    }

    public function like(){
        return $this->hasMany('App\Models\Like', 'user_id', 'id');
    }

    public function form_user(){
        return $this->hasMany('App\Models\Form_user', 'user_id', 'id');
    }

    public function msg(){
        return $this->hasMany('App\Models\Msg', 'expert_id', 'id');
    }
    #################### End Relations #####################
    #################### Begin Implemented Methods ###############

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
    #################### End Implemented Methods ###############
}
