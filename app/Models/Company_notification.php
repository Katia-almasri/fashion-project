<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company_notification extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'company_notifications';
    protected $primaryKey='id';
    protected $fillable = [
        'company_id', 'title', 'details', 'is_seen'
    ];

    ########### relations ##############
   
    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }
}
