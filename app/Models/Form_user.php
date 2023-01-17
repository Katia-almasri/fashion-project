<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form_user extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'form_users';
    protected $primaryKey='id';
    protected $fillable = [
        'form_id', 'user_id', 'rate'
    ];

    ########### relations ##############
   
    public function user_table(){
        return $this->belongsTo('App\Models\Table_user', 'user_id', 'id');
    }

    public function form(){
        return $this->belongsTo('App\Models\Form', 'form_id', 'id');
    }
}
