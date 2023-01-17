<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Msg extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'msg';
    protected $primaryKey='id';
    protected $fillable = [
        'message','image','user_id','expert_id'
    ];

    ########### relations ##############
   
    public function table_user(){
        return $this->belongsTo('App\Models\Table_user', 'user_id', 'id');
    }

    public function expert(){
        return $this->belongsTo('App\Models\Expert', 'expert_id', 'id');
    }
}
