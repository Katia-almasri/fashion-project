<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Piece_details extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'piece_details';
    protected $primaryKey='id';
    protected $fillable = [
        'pieces_id', 'size_id', 'color_id', 'collection_id','image'
    ];

    ########### relations ##############
   
    public function pieces(){
        return $this->belongsTo('App\Models\Piece', 'pieces_id', 'id');
    }

    public function size(){
        return $this->belongsTo('App\Models\Size', 'size_id', 'id');
    }
    public function color(){
        return $this->belongsTo('App\Models\Color', 'color_id', 'id');
    }

    public function pieceDetailsCollection(){
        return $this->hasMany('App\Models\pieceDetails_Collection', 'pieceDetails_id', 'id');
    }

    public function cartCollection(){
        return $this->hasMany('App\Models\CartCollection', 'piece_details_id', 'id');
    }
}
