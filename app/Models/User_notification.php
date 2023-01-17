<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_notification extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'user_notifications';
    protected $primaryKey='id';
    protected $fillable = [
       'user_id', 'title', 'details', 'is_seen'
    ];

    ########### relations ##############
    public function table_user(){
        return $this->belongsTo('App\Models\Table_user', 'user_id', 'id');
    }
}
