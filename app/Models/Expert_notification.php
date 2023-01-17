<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expert_notification extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'expert_notifications';
    protected $primaryKey='id';
    protected $fillable = [
       'expert_id', 'title', 'details', 'is_seen'
    ];

    ########### relations ##############
    public function expert(){
        return $this->belongsTo('App\Models\Expert', 'expert_id', 'id');
    }
}
