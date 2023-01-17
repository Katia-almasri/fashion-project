<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Master_category extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'master_categories';
    protected $primaryKey='id';
    protected $fillable = [
        'name'
    ];

    ########### relations ##############
   
    public function sub_category(){
        return $this->hasMany('App\Models\Sub_category', 'master_category_id', 'id');
    }

    public function piece(){
        return $this->hasMany('App\Models\Piece', 'master_category_id', 'id');
    }
}
