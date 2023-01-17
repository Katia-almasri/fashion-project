<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fashion_news extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'fashion_news';
    protected $primaryKey='id';
    protected $fillable = [
        'admin_id','type', 'title', 'details', 'expert_id', 'company_id'
    ];

    ########### relations ##############
    public function admin(){
        return $this->belongsTo('App\Models\Admin', 'admin_id', 'id');
    }

    public function expert(){
        return $this->belongsTo('App\Models\Expert', 'expert_id', 'id');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }
}
