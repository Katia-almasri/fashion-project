<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'answers';
    protected $primaryKey='id';
    protected $fillable = [
        'type', 'company_id', 'question_id', 'expert_id'
    ];

    ########### relations ##############
   
    public function question(){
        return $this->belongsTo('App\Models\Question', 'question_id', 'id');
    }

    public function expert(){
        return $this->belongsTo('App\Models\Expert', 'expert_id', 'id');
    }
  public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

 }
