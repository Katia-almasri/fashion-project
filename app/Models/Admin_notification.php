<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Admin_notification extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'admin_notifications';
    protected $primaryKey='id';
    protected $fillable = [
        'admin_id', 'title', 'details', 'is_seen'
    ];

    ########### relations ##############
    public function admin(){
        return $this->belongsTo('App\Models\Admin', 'admin_id', 'id');
    }
}
