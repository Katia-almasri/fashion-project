<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'collections';
    protected $primaryKey='id';
    protected $fillable = [
        'name','image','company_id'
    ];

    ########### relations ##############
   
    // public function pieces_details(){
    //     return $this->hasMany('App\Models\Piece_details', 'pieceDetails_id', 'id');
    // }

    public function pieceDetailsCollection(){
        return $this->hasMany('App\Models\pieceDetails_Collection', 'collection_id', 'id');
    }

    
 public function cartCollection(){
    return $this->belongsTo('App\Models\CartCollection', 'collection_id', 'id');
}

public function company(){
    return $this->belongsTo('App\Models\Company', 'company_id', 'id');
}

}
