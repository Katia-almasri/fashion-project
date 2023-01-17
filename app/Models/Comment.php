<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'comments';
    protected $primaryKey='id';
    protected $fillable = [
        'details', 'type', 'expert_id', 'piece_id', 'company_id', 'user_id', 'admin_id'
    ];

    ########### relations ##############
    public function expert(){
        return $this->belongsTo('App\Models\Expert', 'expert_id', 'id');
    }

    public function admin(){
        return $this->belongsTo('App\Models\Admin', 'admin_id', 'id');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    public function table_user(){
        return $this->belongsTo('App\Models\Table_user', 'user_id', 'id');
    }
   
    public function pieces(){
        return $this->belongsTo('Piece', 'piece_id', 'id');
    }


  }
