<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Piece extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'pieces';
    protected $primaryKey='id';
    protected $fillable = [
        'type', 'expert_id', 'sub_category_id', 'season_id', 'usage_id', 'name', 'num_liked', 'price',
        'master_category_id', 'image', 'company_id','gender'
    ];

    ########### relations ##############
   
    public function comment(){//
        return $this->hasMany('App\Models\Comment', 'piece_id', 'id');
    }

    public function like(){//
        return $this->hasMany('App\Models\Like','pieces_id','id');
    }

    public function piece_details(){//
        return $this->hasMany('App\Models\Piece_details', 'pieces_id', 'id');
    }

    public function company(){//
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }
    public function expert(){//
        return $this->belongsTo('App\Models\Expert', 'expert_id', 'id');
    }

    public function sub_category(){//
        return $this->belongsTo('App\Models\Sub_category', 'sub_category_id', 'id');
    }

    public function master_category(){//
        return $this->belongsTo('App\Models\Master_category', 'master_category_id', 'id');
    }

    public function season(){//
        return $this->belongsTo('App\Models\Season', 'season_id', 'id');
    }

    public function usage(){//
        return $this->belongsTo('App\Models\Usage', 'usage_id', 'id');
    }

    public function cartCollection(){
        return $this->hasMany('App\Models\CartCollection', 'piece_id', 'id');
    }

    public function pieceDetailsCollection(){
        return $this->hasMany('App\Models\pieceDetails_Collection', 'piece_id', 'id');
    }
    
}
