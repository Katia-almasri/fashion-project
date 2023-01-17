<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'follows';
    protected $primaryKey='id';
    protected $fillable = [
        'company_id', 'user_id'
    ];

    ########### relations ##############
   
    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    public function table_user(){
        return $this->belongsTo('App\Models\Table_user', 'user_id', 'id');
    }

}
