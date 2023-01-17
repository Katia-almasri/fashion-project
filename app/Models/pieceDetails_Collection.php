<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pieceDetails_Collection extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'piecedetailscollection';
    protected $primaryKey='id';
    protected $fillable = [
        'collection_id', 'pieceDetails_id', 'piece_id'
    ];

    ########### relations ##############
   
    public function pieceDetails(){
        return $this->belongsTo('App\Models\Piece_details', 'pieceDetails_id', 'id');
    }

    public function pieces(){
        return $this->belongsTo('App\Models\Piece', 'piece_id', 'id');
    }
 
    public function collections(){
        return $this->belongsTo('App\Models\Collection', 'collection_id', 'id');
    }
    
}
