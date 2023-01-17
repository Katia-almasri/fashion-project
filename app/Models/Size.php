<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Size extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'size';
    protected $primaryKey='id';
    protected $fillable = [
         'name'
    ];

    ########### relations ##############
    public function pieceDetails(){
        return $this->hasMany('App\Models\Piece_details', 'size_id', 'id');
    }
}
