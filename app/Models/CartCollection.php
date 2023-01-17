<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartCollection extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'cart_collections';
    protected $primaryKey='id';
    protected $fillable = [
        'company_id', 'piece_details_id', 'type', 'piece_id'
    ];

    ########### relations ##############
   
    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    public function pieceDetails(){
        return $this->belongsTo('App\Models\Piece_details', 'piece_details_id', 'id');
    }

    public function pieces(){
        return $this->belongsTo('App\Models\Piece', 'piece_id', 'id');
    }

 }
