<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sub_category extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'sub_categories';
    protected $primaryKey='id';
    protected $fillable = [
        'master_category_id', 'name'
    ];

    ########### relations ##############
   
    public function pieces(){
        return $this->hasMany('App\Models\Piece', 'sub_category', 'id');
    }

    public function master_category(){
        return $this->belongsTo('App\Models\Master_category', 'master_category_id', 'id');
    }
}
