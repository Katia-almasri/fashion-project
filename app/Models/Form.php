<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'forms';
    protected $primaryKey='id';
    protected $fillable = [
       'company_id', 'season_id', 'average_rate','year','name_form'
    ];

    ########### relations ##############

    public function user_form(){
        return $this->hasMany('App\Models\Form_user','form_id','id');
    }
    /////formuser,season

    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    public function season(){
        return $this->belongsTo('App\Models\Season', 'season_id', 'id');
    }
}

