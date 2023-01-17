<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Star extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'stars';
    protected $primaryKey='id';
    protected $fillable = [
        'company_id', 'usage_id', 'image', 'star_name', 'details'
    ];

    ########### relations ##############
    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    public function uage(){
        return $this->belongsTo('App\Models\Usage', 'usage_id', 'id');
    }
}
