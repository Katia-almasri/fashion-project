<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Authenticatable implements JWTSubject
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
        use SoftDeletes;
    protected $table = 'companies';
    protected $primaryKey='id';
    protected $fillable = [
        'name', 'email', 'password', 'location', 'major_category', 'image', 'num_followed'
    ];
    protected $dates = ['deleted_at'];

    ########### relations ##############
   
   
    public function comment(){  //
        return $this->hasMany('App\Models\Comment', 'company_id', 'id');
    }

    public function company_notification(){ //
        return $this->hasMany('App\Models\Company_notification', 'company_id', 'id');
    }

    public function pieces(){ //
        return $this->hasMany('App\Models\Piece', 'company_id', 'id');
    }

    public function follow(){ //
        return $this->hasMany('Follow' ,'company_id','id');
    }

    public function form(){ //
        return $this->hasMany('App\Models\Form', 'company_id', 'id');
    }

    public function fashion_news(){
        return $this->hasMany('App\Models\fashion_news', 'type_id', 'id');
    }

    public function cartCollection(){
        return $this->hasMany('App\Models\CartCollection', 'company_id', 'id');
    }
    public function Collection(){
        return $this->hasMany('App\Models\Collection', 'company_id', 'id');
    }
    ############################## end relations########################

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
