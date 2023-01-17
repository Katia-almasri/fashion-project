<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'questions';
    protected $primaryKey='id';
    protected $fillable = [
        'details', 'user_id'
    ];

    ########### relations ##############
    public function answer(){
        return $this->hasMany('Answer');
    }

    public function table_user(){
        return $this->belongsTo('App\Models\Table_user', 'user_id', 'id');
    }

   
}
