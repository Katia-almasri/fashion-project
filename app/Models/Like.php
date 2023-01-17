<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'likes';
    protected $primaryKey='id';
    protected $fillable = [
        'pieces_id', 'user_id'
    ];

    ########### relations ##############
   
    public function table_user(){
        return $this->belongsTo('App\Models\Table_user', 'user_id', 'id');
    }

    public function pieces(){
        return $this->belongsTo('App\Models\Piece', 'pieces_id', 'id');
    }
}
