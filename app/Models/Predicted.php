<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Predicted extends Model
{
    protected $table = 'predicted';
    protected $primaryKey='id';
    protected $fillable = [
       'ds', 'yhat', 'yhat_lower', 'yhat_upper'
    ];

    ##################### Begin Relations ##########################
    public function colors(){
        return $this->belongsTo('App\Models\Color', 'yhat_upper', 'id');
    }

}
