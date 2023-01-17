<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\adminRequest;  //
use App\Traits\GeneralTrait;
use Auth;
use Validator;
use Session;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Models\Table_user;
use App\Models\Expert;
use App\Models\Admin;
use App\Models\Usage;
use App\Models\Fashion_news;
use App\Models\Season;
use App\Models\Sub_category;
use App\Models\Piece;
use App\Models\Color;
use App\Models\Size;
use App\Models\Master_category;
use App\Models\Piece_details;
use App\Models\pieceDetails_Collection;
use App\Models\Collection;
use App\Models\Form;
use App\Models\User_notification;
use Carbon\Carbon;
use App\Models\Predicted;



class HomeController extends Controller
{
    
    use GeneralTrait;
   
    public function index()
        {
        return view('home');
    }

    public function displayAllFashionNews(Request $request){
        $credentials = $request->only([]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        $fashionNewsCompany = Fashion_news::with('admin','expert','company')
        ->whereHas('expert',function($query) {
        $query->where('experts.deleted_at','=',NULL);
        })
        ->orwhereHas('company',function($query) {
        $query->where('companies.deleted_at','=',NULL);
        })
        ->orwhereHas('admin')
        ->get();
        return response()->json(
            $fashionNewsCompany
        );

    }
    
    public function displayLastFashionNews(Request $request){
        $credentials = $request->only([]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        $fashionNewsCompany = Fashion_news::with('admin','expert','company')
        ->whereHas('expert',function($query) {
        $query->where('experts.deleted_at','=',NULL);
        })
        ->orwhereHas('company',function($query) {
        $query->where('companies.deleted_at','=',NULL);
        })
        ->orwhereHas('admin')
        ->latest()->limit(LIMIT_NUM_NEWS)->get();
        return response()->json(
            $fashionNewsCompany
        );

    }

    public function displayLatestpieces(Request $request){
        $credentials = $request->only([]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        $pieces = Piece::with('usage', 'season', 'sub_category', 'master_category', 'company','expert')
        ->whereHas('expert',function($query) {
        $query->where('experts.deleted_at','=',NULL);
        })
        ->orwhereHas('company',function($query) {
        $query->where('companies.deleted_at','=',NULL);
        })
        ->latest()->limit(3)->get();
        return response()->json(
            $pieces
        );

    }

    public function displayAllPieces(Request $request){
        $credentials = $request->only([]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        $pieces = Piece::with('like', 'usage', 'season', 'sub_category', 'master_category','company','expert')
        ->whereHas('expert',function($query) {
        $query->where('experts.deleted_at','=',NULL);
        })
        ->orwhereHas('company',function($query) {
        $query->where('companies.deleted_at','=',NULL);
        })
        ->get();
        return response()->json($pieces);

    }

    public function displaypiecesDetails(Request $request, $piece_id){
        $credentials = $request->only([]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        $piecesDetails= Piece::with(['usage', 'season', 'sub_category', 'master_category', 'comment'=>function($q1){
            $q1->with('company','expert','table_user');
        }])
        ->where('id','=',$piece_id)->with(['piece_details' => function($q) use($piece_id) {
            $q->with('color','size');
        }])->get();

        return response()->json(
            $piecesDetails
        );

    }

    public function displayCompaniesCollection(Request $request){
        $credentials = $request->only([]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        
        $Collection = Collection::with('company')->get();
        return response()->json($Collection);
    }

    public function displayDetailsCollection(Request $request, $id){
        $credentials = $request->only([]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $company = Auth::guard('user-api')->user();
    
        $collectionDetails= pieceDetails_Collection::
         with( 'pieceDetails', 'pieces')
         ->whereHas('pieceDetails',function($query) use($id) {
            $query->where('piecedetailscollection.pieceDetails_id','>',0)->where('collection_id', $id);
            })
        ->orwhereHas('pieces',function($query) use($id) {
            $query->where('piecedetailscollection.piece_id','>',0)->where('collection_id', $id);
            })
        
        ->get();
        
        return response()->json($collectionDetails);
    }
    

      ################### Filtering ###########################
      public function filterRequests(Request $request , $sub_category,   $master_category,  $type, $type_id){
       
        $credentials = $request->only([]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();

        $userId = $user->id;
        $color = $request['color'];
        $size = $request['size'];
        $usage = $request['usage']; 
        $season = $request['season']; 
        $gender = $request['gender'];
        if( $sub_category!=0 && $master_category!=0 ){
            $filters = Piece::
            where([
                ['sub_category_id', $sub_category],
                ['master_category_id', $master_category]
            ]);
        }
        else {
            if($type =='expert')
                $filters = Piece::with('expert')
                ->where('expert_id', '=', $type_id);
                
            else if($type=='company')
                $filters = Piece::with('company')
                ->where('company_id', '=', $type_id);
        }
                    
            
            if($gender!=null){
                $filters = $filters
                ->where('gender', $gender);
         
            }

            if($usage!=null){
                $filters = $filters
                ->whereIn('usage_id', $usage);
            }

            if($season!=null){
                $filters = $filters
                ->whereIn('season_id', $season);
                }

            if($color!=null){
                $filters =  $filters
                ->with(['piece_details'=>function($q){
                        $q->with('color', 'size');
                }])->whereHas('piece_details.color',function($q) use($color){
                    $q->whereIn('color.id', $color);
                });
            }

            if($size!=null){
                $filters =  $filters->with(['piece_details'=>function($q){
                        $q->with('color', 'size');
                }])->whereHas('piece_details.size',function($q) use($size){
                    $q->whereIn('size.id', $size);
                });
            }
             return response()->json(
                $filters->with('usage', 'season', 'master_category', 'sub_category')->get()

            );
        
    }

    ######################### Search ####################
public function autocompleteSearch(Request $request)
    {
      $q = $request['query'];
      
      $resultPiece = Piece::where('name','like','%'.$q.'%')->get();
      $resultCompany = Company::where([
        ['name','like','%'.$q.'%'],
        ['deleted_at', '!=', null]
        ])
        ->get();
        
      $resultExpert = Expert::where([
        ['name','like','%'.$q.'%'],
        ['deleted_at', '!=', null]
        ])->get();
    return response()->json(array("pieces"=>$resultPiece,
                                  "company"=>$resultCompany,
                                  "expert"=>$resultExpert
                                ));
    
    
} 

public function searchInBox(Request $request){
    $q = $request['query'];
    $delimiter = ' ';
    $words = explode($delimiter, $q);
    $resultPieces = Piece::with('usage', 'season', 'sub_category', 'master_category')
    ->where(function ($query) use ($words) {
        foreach ($words as $word) {
            $query->Where('name', 'like', "%{$word}%")
            ->orWhere('name', 'like', "%{$word}%"); 
             
        }
       
    })->get();
    foreach ($resultPieces as $piece) {
        foreach ($words as $word) {
        
            $piece->count+=  substr_count($piece, $word);
            $piece->type = "piece";
            
        }     
         
    }
   
    {
        $resultCompanies = Company::where(function ($query) use ($words) {
            foreach ($words as $word) {
                $query->Where('name', 'like', "%{$word}%")
                ->where('deleted_at', '!=', null)
                ->orWhere('name', 'like', "%{$word}%");
                
                 
            }
           
        })->get();
        foreach ($resultCompanies as $company) {
            foreach ($words as $word) {
            
                $company->count+=  substr_count($company, $word);
                $company->type = "company";
                 
            }     
             
        }
      
      {
        $resultExperts = Expert::where(function ($query) use ($words) {
            foreach ($words as $word) {
                $query->Where('name', 'like', "%{$word}%")
                ->where('deleted_at', '!=', null)
                ->orWhere('name', 'like', "%{$word}%");
                
                 
            }
           
        })->get();
        foreach ($resultExperts as $expert) {
            foreach ($words as $word) {
            
                $expert->count+=  substr_count($expert, $word);
                $expert->type = "expert";
                 
            }     
             
        }
        
      }

      
      
      if(count($resultCompanies)>0 ||count($resultPieces)>0  || count($resultExperts)>0 ){
        $merged1 = $resultPieces->merge($resultCompanies, $resultExperts);
        $result1 = $merged1->all();
        $c = collect($result1);
        $sorted = $c->sortByDesc('count')->all();
        return response()->json($sorted);
      }
     

      else
        return response()->json([
            "status"=>false,
            "message"=>"no results found!!"
        ]);
    
}
}

################## recommendations ########################
public function recommendations1(Request $request, $pieceId){
    $piece = Piece::find($pieceId);
    //1. get season
    $seasonId = $piece->season_id;
    $masterCategoryId = $piece->master_category_id;
    if($masterCategoryId==1){
        if($piece->sub_category_id==1 || $piece->sub_category_id==4 || $piece->sub_category_id==8){
            $recommendations = Piece::with('company',
            'expert',
            'usage',
            'season',
            'master_category',
            'sub_category')
            
            ->whereHas('expert',function($query) use($seasonId, $piece) {
                $query->where([
                    ['experts.deleted_at','=',NULL],
                    ['season_id', '=', $seasonId],
                    ['gender', '=', $piece->gender],
                    ['master_category_id', '=', 1],
                    ['sub_category_id', '=', 2],
                ])
                ->orWhere([
                    ['experts.deleted_at','=',NULL],
                    ['season_id', '=', $seasonId],
                    ['gender', '=', $piece->gender],
                    ['master_category_id', '=', 1],
                    ['sub_category_id', '=', 5],
                ])
                ->orWhere([
                    ['experts.deleted_at','=',NULL],
                    ['season_id', '=', $seasonId],
                    ['gender', '=', $piece->gender],
                    ['master_category_id', '=', 1],
                    ['sub_category_id', '=', 9],
                ]);
            })
            ->orwhereHas('company',function($query) use($seasonId, $piece) {
                $query->where([
                    ['companies.deleted_at','=',NULL],
                    ['season_id', '=', $seasonId],
                    ['gender', '=', $piece->gender],
                    ['master_category_id', '=', 1],
                    ['sub_category_id', '=', 2],
                ])
                ->orWhere([
                    ['companies.deleted_at','=',NULL],
                    ['season_id', '=', $seasonId],
                    ['gender', '=', $piece->gender],
                    ['master_category_id', '=', 1],
                    ['sub_category_id', '=', 5],
                ])
                ->orWhere([
                    ['companies.deleted_at','=',NULL],
                    ['season_id', '=', $seasonId],
                    ['gender', '=', $piece->gender],
                    ['master_category_id', '=', 1],
                    ['sub_category_id', '=', 9],
                ]);
            })
            ->get();
            

            return response()->json($recommendations);
        }
        else if($piece->sub_category_id==2 || $piece->sub_category_id==5 || $piece->sub_category_id==9){
           
            
            $recommendations = Piece::with('company',
            'expert',
            'usage',
            'season',
            'master_category',
            'sub_category')
            
            ->whereHas('expert',function($query) use($seasonId, $piece) {
                $query->where([
                    ['experts.deleted_at','=',NULL],
                    ['season_id', '=', $seasonId],
                    ['gender', '=', $piece->gender],
                    ['master_category_id', '=', 1],
                    ['sub_category_id', '=', 1],
                ])
                ->orWhere([
                    ['experts.deleted_at','=',NULL],
                    ['season_id', '=', $seasonId],
                    ['gender', '=', $piece->gender],
                    ['master_category_id', '=', 1],
                    ['sub_category_id', '=', 4],
                ])
                ->orWhere([
                    ['experts.deleted_at','=',NULL],
                    ['season_id', '=', $seasonId],
                    ['gender', '=', $piece->gender],
                    ['master_category_id', '=', 1],
                    ['sub_category_id', '=', 8],
                ]);
                })
            ->orwhereHas('company',function($query) use($seasonId, $piece) {
                $query->where([
                    ['companies.deleted_at','=',NULL],
                    ['season_id', '=', $seasonId],
                    ['gender', '=', $piece->gender],
                    ['master_category_id', '=', 1],
                    ['sub_category_id', '=', 1],
                ])
                ->orWhere([
                    ['companies.deleted_at','=',NULL],
                    ['season_id', '=', $seasonId],
                    ['gender', '=', $piece->gender],
                    ['master_category_id', '=', 1],
                    ['sub_category_id', '=', 4],
                ])
                ->orWhere([
                    ['companies.deleted_at','=',NULL],
                    ['season_id', '=', $seasonId],
                    ['gender', '=', $piece->gender],
                    ['master_category_id', '=', 1],
                    ['sub_category_id', '=', 8],
                ]);
            })
            ->get();
            
            return response()->json($recommendations);
        }
        
            
    }
    else {
        $recommendations = Piece::with('company',
        'expert',
        'usage',
        'season',
        'master_category',
        'sub_category')
        
        ->whereHas('expert',function($query) use($seasonId, $piece) {
            $query->where([
                ['experts.deleted_at','=',NULL],
                ['season_id', '=', $seasonId],
                ['gender', '=', $piece->gender]
            ])
             ->where('master_category_id',1);
            })
        ->orwhereHas('company',function($query) use($seasonId, $piece) {
        $query->where([
            ['companies.deleted_at','=',NULL],
            ['season_id', '=', $seasonId],
            ['gender', '=', $piece->gender]
            ])->where('master_category_id', 1);
        })
        ->get();

        return response()->json($recommendations);
    }
    
}
    public function recommendations2(Request $request, $pieceId){

        $piece = Piece::find($pieceId);
        //1. get season
        $seasonId = $piece->season_id;
        $masterCategoryId = $piece->master_category_id;
        $recommendations = Piece::with('company',
        'expert',
        'usage',
        'season',
        'master_category',
        'sub_category')
        
        ->whereHas('expert',function($query) use($seasonId, $piece) {
            $query->where([
                ['experts.deleted_at','=',NULL],
                ['season_id', '=', $seasonId],
                ['gender', '=', $piece->gender]
            ])
        ->where('master_category_id',2);
            })
        ->orwhereHas('company',function($query) use($seasonId, $piece) {
            $query->where([
                ['companies.deleted_at','=',NULL],
                ['season_id', '=', $seasonId],
                ['gender', '=', $piece->gender]
                ])->where('master_category_id', 2);
            })
        ->get();

        return response()->json($recommendations);


    }

    public function recommendations3(Request $request, $pieceId){
        $piece = Piece::find($pieceId);
        //1. get season
        $seasonId = $piece->season_id;
        $masterCategoryId = $piece->master_category_id;

        $recommendations = Piece::with('company',
        'expert',
        'usage',
        'season',
        'master_category',
        'sub_category')
        
        ->whereHas('expert',function($query) use($seasonId, $piece) {
            $query->where([
                ['experts.deleted_at','=',NULL],
                ['season_id', '=', $seasonId],
                ['gender', '=', $piece->gender]
            ])
        ->where('master_category_id',3);
            })
        ->orwhereHas('company',function($query) use($seasonId, $piece) {
        $query->where([
            ['companies.deleted_at','=',NULL],
            ['season_id', '=', $seasonId],
            ['gender', '=', $piece->gender]
            ])->where('master_category_id', 3);
        })
        ->get();

        return response()->json($recommendations);


    }
public function youMayLike($pieceId){
    $piece = Piece::find($pieceId);
    $seasonId = $piece->season_id;
    $sub_categoryId = $piece->sub_category_id;
    $master_categoryId = $piece->master_category_id;
    $like = Piece::with('company',
    'expert',
    'usage',
    'season',
    'master_category',
    'sub_category')
    ->where([
        ['id', '!=', $pieceId],
        ['season_id', '=', $seasonId],
        ['sub_category_id', '=', $sub_categoryId],
        ['master_category_id', '=', $master_categoryId]
    ])
    ->whereHas('expert',function($query) {
        $query->where('experts.deleted_at','=',NULL);
    })
    ->orwhereHas('company',function($query) {
    $query->where('companies.deleted_at','=',NULL);
    })
    ->inRandomOrder()
    ->get();
    return response()->json($like);
}

public function sortBy(Request $request){
    $sortById = $request['sortById'];
    $sortByMethodId = $request['sortByMethodId'];
    if($sortByMethodId==1) //ASC order
        $sortByMethod = 'ASC';
    else if($sortByMethodId==2) //DESC order
    $sortByMethod = 'DESC';

    //most price
    if($sortById==1){
        $mostPrice = Piece::with('company',
        'expert',
        'usage',
        'season',
        'master_category',
        'sub_category')
        ->orderBy('price', $sortByMethod)
        ->whereHas('expert',function($query) {
            $query->where('experts.deleted_at','=',NULL);
            })
        ->orwhereHas('company',function($query) {
        $query->where('companies.deleted_at','=',NULL);
        })
        ->get();
        return response()->json($mostPrice);
    }
    //most pieces liked
    if($sortById==2){
        $mostLiked = Piece::with('company',
        'expert',
        'usage',
        'season',
        'master_category',
        'sub_category')
        ->orderBy('num_liked', $sortByMethod)
        ->whereHas('expert',function($query) {
            $query->where('experts.deleted_at','=',NULL);
            })
        ->orwhereHas('company',function($query) {
        $query->where('companies.deleted_at','=',NULL);
        })
        ->get();
        return response()->json($mostLiked);
    }
    //most followed companies
    if($sortById==3){
        $mostFollowed =DB::table('pieces')
        ->select('pieces.*', 'companies.*')
        ->join('companies', 'pieces.company_id', '=', 'companies.id')
        ->where('pieces.type', 'company')
        ->where('companies.deleted_at', '=', null)
        ->orderBy('num_followed', $sortByMethod)
        ->get();
        return response()->json($mostFollowed);
    }

}

public function displayNotifications(Request $request){
    $credentials = $request->only(['details']);
     $token = auth()->guard('user-api')->attempt($credentials);
     $user = Auth::guard('user-api')->user();
     $userId = $user->id;
    $notifications = User_notification::where([
        ['user_id', '=', $userId],
        ['is_seen', '=', 0]
        ])->get();
    $count = $notifications->count('id');
    return response()->json([
        "status"=>true,
        "notifications"=>$notifications,
        "count"=>$count
    ]);
}
    #################### Begin Notifications ################################
public function readNotification(Request $request, $id){
    $credentials = $request->only(['details']);
     $token = auth()->guard('user-api')->attempt($credentials);
     $user = Auth::guard('user-api')->user();
     $userId = $user->id;

    $readNotification = User_notification::find($id);
    $readNotification->is_seen = 1;
    $readNotification->save();
    return response()->json([
        "status"=>true,
        "message"=>$readNotification->title,
        "data"=>$readNotification
    ]);


}
    #################### End Notifications ################################
    #################### Begin important routes ################################
public function getSubCategoriesBelongToMaster(Request $request, $masterCategory){
    $credentials = $request->only(['details']);
    $token = auth()->guard('user-api')->attempt($credentials);
    $user = Auth::guard('user-api')->user();
    $userId = $user->id;

    $pieces = Sub_category::with('master_category')
    ->where('master_category_id', '=', $masterCategory)
    ->get();
    return response()->json($pieces);
}

public function getApparelPieces(Request $request, $subCategory){
    $credentials = $request->only(['details']);
    $token = auth()->guard('user-api')->attempt($credentials);
    $user = Auth::guard('user-api')->user();
    $userId = $user->id;

    $pieces = Piece::with('like', 'usage', 'season', 'sub_category', 'master_category','company','expert')
    ->where([
        ['master_category_id', '=', 1],
        ['sub_category_id', '=', $subCategory]
    ])
    ->get();
    return response()->json($pieces);
}

public function getAccessoriesPieces(Request $request, $subCategory){
    $credentials = $request->only(['details']);
    $token = auth()->guard('user-api')->attempt($credentials);
    $user = Auth::guard('user-api')->user();
    $userId = $user->id;

    $pieces = Piece::with('like', 'usage', 'season', 'sub_category', 'master_category','company','expert')
    ->where([
        ['master_category_id', '=', 2],
        ['sub_category_id', '=', $subCategory]
    ])
    ->get();
    return response()->json($pieces);
}

public function getFootwearPieces(Request $request, $subCategory){
    $credentials = $request->only(['details']);
    $token = auth()->guard('user-api')->attempt($credentials);
    $user = Auth::guard('user-api')->user();
    $userId = $user->id;

    $pieces = Piece::with('like', 'usage', 'season', 'sub_category', 'master_category','company','expert')
    ->where([
        ['master_category_id', '=', 3],
        ['sub_category_id', '=', $subCategory]
    ])
    ->get();
    return response()->json($pieces);  

}  

#################### End important routes ################################
    public function getCompanies(Request $request){
        $credentials = $request->only(['details']);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        $userId = $user->id;

        $companies = Company::get();
        return response()->json($companies);
    }

    public function getExperts(Request $request){
        $credentials = $request->only(['details']);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        $userId = $user->id;

        $experts = Expert::get();
        return response()->json($experts);
    }


    public function getPredictedPieces(Request $request){
        $credentials = $request->only([]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        
        $currentMonths = $this->getSeason();
        $piecesDetails = Predicted:: with(['colors'=>function($q){
            $q->with('pieceDetail');
        }])
        ->whereMonth('ds','>=', $currentMonths[0])
        ->whereMonth('ds','<=', $currentMonths[2])
        ->whereYear('ds', '=', Carbon::now()->year)
        ->get();
    
        return response()->json($piecesDetails);
    
    }
   
    public function visitCompanyOrExpert(Request $request, $companyId, $expertId){
        $credentials = $request->only([]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();

        if($companyId!=0){
            $companyProfile = Company::select('*')
            ->where('id', '=', $companyId) 
            ->get();
            return response()->json($companyProfile);
        }
        else if($expertId!=0){
            $expertProfile = Expert::select('*')
            ->where('id', '=', $expertId) 
            ->get();
            return response()->json($expertProfile);
        }


    }

    public function getPiecesForCompany(Request $request,$company_id){
        $credentials = $request->only(['details']);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        $userId = $user->id;
    
        $pieces = Piece::with('like', 'usage', 'season', 'sub_category', 'master_category','company')
        ->where([
            ['company_id',$company_id]
        ])
        ->get();
        return response()->json($pieces);  
        }


    public function getPiecesForExpert(Request $request,$expert_id){
            $credentials = $request->only(['details']);
            $token = auth()->guard('user-api')->attempt($credentials);
            $user = Auth::guard('user-api')->user();
            $userId = $user->id;
            $pieces = Piece::with('like', 'usage', 'season', 'sub_category', 'master_category','expert')
            ->where([
                ['expert_id',$expert_id]
            ])
            ->get();
            return response()->json($pieces);  
            }
}








    





        
    

