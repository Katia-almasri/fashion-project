<?php

namespace App\Http\Controllers;
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
use App\Models\Msg;
use App\Models\Comment;
use App\Models\User_notification;
use App\Models\Expert_notification;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;
use App\Models\Predicted;
use Carbon\Carbon;

class ExpertController extends Controller
{
    

    use GeneralTrait;
    public function login(Request $request)
    {

        try {
            $rules = [
                "email" => "required|email|max:255",
                "password" => "required|min:3|max:51"

            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($validator, $code);
            }

            // //login
                 
             $credentials = $request->only(['email', 'password']);
             $token = auth()->guard('expert-api')->attempt($credentials);
            if (!$token)
                return $this->returnError('E001', 'error in inputs');

            $expert = Auth::guard('expert-api')->user();
            $expert->api_token = $token;
            //return token
            return $this->returnData('expert', $expert);

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }


    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
            "email" => "required|email|max:255",
            "password" => "required|min:3|max:51",
            "gender"=>"required",
            "name"=>"required",
            "date_of_birth"=>"required|date"
        ]);  
        if ($validator->fails()) {  
            return response()->json(['status'=>false, 
            'message'=>$validator->errors()->all()
        ]);
        } 

        $expert = new Expert();
        $expert->email = $request->email;
        $expert->name = $request->name;
        $expert->date_of_birth = $request->date_of_birth;
        $expert->gender = $request->gender;
        $expert->image ='user.jpg';
        $expert->password = bcrypt($request->password);
         //////////////////////attampt //////////////////////////
        //1.search in expert table
            $expertInCompanies =Company::where('email', '=', $expert->email)->first();
            $expertInUser = Table_user::where('email', '=', $expert->email)->first();
            $expertInExpert =Expert::where('email', '=', $expert->email)->first();
            $expertInAdmin = Admin::where('email', '=', $expert->email)->first();
            if($expertInCompanies==null && $expertInUser==null && $expertInExpert==null && $expertInAdmin==null){
                $expert->save();
                
            return $this->login($request);
            return response()->json([
            'success' => true,
            'data' => $user
            ], Response::HTTP_OK);
        }
            else
                return  $this -> returnError('','email Already Used');
            ////////////////////end attempt ///////////////////////////
    }

    public function logout(Request $request)
    {
         $token = $request -> header('auth-token');
        if($token){
            try {

                JWTAuth::setToken($token)->invalidate(); //logout
            }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                return  $this -> returnError('','some thing went wrongs');
            }
            return $this->returnSuccessMessage('Logged out successfully');
        }else{
            $this -> returnError('','some thing went wrongs');
        }

    }

    public function displayExpertProfile(Request $request){
        $credentials = $request->only(['id']);
        $token = auth()->guard('expert-api')->attempt($credentials);
        $expert = Auth::guard('expert-api')->user();
        return response()->json([
            $expert
        ]); 
    }

    public function editExpertProfile(Request $request){
         
      
        $credentials = $request->only(['id']);
        $token = auth()->guard('expert-api')->attempt($credentials);
        $expert = Auth::guard('expert-api')->user();
        return response()->json([
            "name"=>$expert->name,
            "email"=>$expert->email,
            "password"=>$expert->password,
            "date_of_birth"=>$expert->date_of_birth,
            "gender"=>$expert->gender,
            "details"=>$expert->details,
            "image"=>$expert->image
        ]); 
    }

    public function updateExpertProfile(Request $request){
           $validator = Validator::make($request->all(), 
        [ 
            "email" => "required|email|max:255",
            "password" => "min:8|max:51",
            "name"=>"required"
        
        ]);  
        if ($validator->fails()) {  
            return response()->json(['status'=>false, 
            'message'=>$validator->errors()->all()
        ]);
        }

        $credentials = $request->only([
            'name',
            'email',
            'date_of_birth',
            'details',
            'image',
            'password'
        
        ]);
        $token = auth()->guard('expert-api')->attempt($credentials);
        $expert = Auth::guard('expert-api')->user();
        $expert->name = $request['name'];
        $expert->email = $request['email'];
        if($request['password']!=null && !Hash::check($request['password'],  $expert->password)){
            $expert->password = Hash::make($request['password']);
        }
       
        if($request['image']!=null){
            $image_name = $this->saveImage($request['image'], 'storage/images/expertImages');
            if($image_name->original['status']==false)
                return response()->json($image_name->original['message']);
            $expert->image = $image_name->original['message'];
        }
        if($request['date_of_birth']!=null)
            $expert->date_of_birth = $request['date_of_birth'];
        if($request['details']!=null)
            $expert->details = $request['details'];
        $expert->gender = $expert->gender;
        //gender
        $expert->gender = $expert->gender;
        $expert->save();

        return response()->json([
            "status"=>true,
            "message"=>"updated successfully"
        ]);  

    }

    public function addFashionNews(Request $request){
        $validator = Validator::make($request->all(), 
        [ 
            "details" => "required|max:255",
            "image" => "required",   
        ]);  
        if ($validator->fails()) {  
            return response()->json(['status'=>false, 
            'message'=>$validator->errors()->all()
]);
        } 
        

        $credentials = $request->only([]);
        $token = auth()->guard('expert-api')->attempt($credentials);
        $expert = Auth::guard('expert-api')->user();

        //input the details
        $fashionNews = new Fashion_news();
        $fashionNews->details = $request->details;
        if($request['image']!=null){
            $image_name = $this->saveImage($request['image'], 'storage/images/fashionNewsImages');
            if($image_name->original['status']==false)
                return response()->json($image_name->original['message']);
            $fashionNews->image = $image_name->original['message'];
        }
        $fashionNews->type = "expert";
        $fashionNews->expert_id = $expert->id;
        $fashionNews->admin_id = 0;
        $fashionNews->company_id = 0;
        $fashionNews->save();

        return response()->json([
            "status"=>true,
            "message"=>"fashion news added successfully"
        ]);

    }
    public function displayMyNews(Request $request){
        $credentials = $request->only([]);
        $token = auth()->guard('expert-api')->attempt($credentials);
        $expert = Auth::guard('expert-api')->user();
        $expertId = $expert->id;
           $news = Fashion_news::where('expert_id','=',$expertId)->get();
  
        return response()->json($news);
    }

    public function deleteMyNews(Request $request, $news_id)
{   
    $News = Fashion_news::where('id', $news_id);
    if(!$news_id){
    return response()->json([
        "status"=>false,
        "message"=>"Not found piece"
    ]);
    }
    else{
    $News->delete();
    return response()->json([
        "status"=>true,
        "message"=>"piece deleted successfully"
    ]);
    }
}

public function addPiece(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
        "name" => "required|max:50",
        "originalImage"=>"required",
        "price"=>"nullable|numeric|min:0",
        "usage_id"=>"numeric|required", 
        "season_id"=>"numeric|required", 
        "master_category_id"=>"numeric|required", 
        "sub_category_id"=>"numeric|required", 
            
    ]);  
    if ($validator->fails()) {  
        return response()->json(['status'=>false, 
        'message'=>$validator->errors()->all()
]);
    }

    $credentials = $request->only([
         
    ]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $piece = new Piece();
    $piece->name = $request['name'];
    if($request['price']!=null)
        $piece->price = $request['price'];
    $piece->type = "expert";
    $piece->expert_id = $expert->id;
    $piece->company_id = 0;
    $piece->sub_category_id =  $request['sub_category_id'];
    $piece->master_category_id =  $request['master_category_id'];
    $piece->season_id =$request['season_id'];
    $piece->usage_id = $request['usage_id'];

    if($request['originalImage']!=null){
        $image_name = $this->saveImage($request['originalImage'], 'storage/images/piecesImages');
        if($image_name->original['status']==false)
            return response()->json($image_name->original['message']);
        $piece->image = $image_name->original['message'];
    }
    $piece->save();

    //add details
    $pieceDetails = new Piece_details();
    if($request['color_id']!=null)
        $pieceDetails->color_id =  $request['color_id'];
    if($request['size_id']!=null)
    $pieceDetails->size_id =  $request['size_id'];
    if($request['coloredImage']!=null){
        $image_name = $this->saveImage($request['coloredImage'], 'storage/images/piecesImages/details');
        if($image_name->original['status']==false)
            return response()->json($image_name->original['message']);
        $pieceDetails->image = $image_name->original['message'];
    }

    
    $piece->piece_details()->save($pieceDetails);

    return response()->json([
        "status"=>true,
        "message"=>"piece added successfully"
    ]);
}

public function editmypiece(Request $request, $piece_id){
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $expertId = $expert->id;
    $pieceId = $piece_id;
    $piece = Piece::with(['season', 'sub_category', 'master_category', 'usage'])
    ->whereHas('season', function($q){
        $q->select(['name', 'id']);
    })
    ->whereHas('sub_category', function($q){
        $q->select(['name', 'id']);
    })
    ->whereHas('master_category', function($q){
        $q->select(['name', 'id']);
    })
    ->whereHas('usage', function($q){
        $q->select(['name', 'id']);
    })
    ->where([
        ['type', 'expert'],
        ['expert_id', $expertId],
        ['id', $pieceId]
    ])
    ->get();

    return response()->json($piece); 
}

public function updatePiece(Request $request , $piece_id){
    $validator = Validator::make($request->all(), 
    [ 
        "name"=>"required|min:3|max:255",
        "image"=>"required"
    
    ]);  
    if ($validator->fails()) {  
        return response()->json(['status'=>false, 
        'message'=>$validator->errors()->all()
]);
    }
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();

    $piece = Piece::find($piece_id);

    $piece->name = $request['name'];

    $image_name = $this->saveImage($request['image'], 'storage/images/piecesImages');
    if($image_name->original['status']==false)
        return response()->json($image_name->original['message']);
    $piece->image = $image_name->original['message'];  

    if($request['usage_id']!=null)
        $piece->usage_id = $request['usage_id'];
    
    if($request['season_id']!=null)
        $piece->season_id = $request['season_id'];

    if($request['sub_category_id']!=null)
        $piece->sub_category_id = $request['sub_category_id'];

    if($request['master_category_id']!=null)
        $piece->master_category_id = $request['master_category_id'];

    if($request['price']!=null)
        $piece->price = $request['price'];
    $piece->save();

    return response()->json([
        "status"=>true,
        "message"=>"piece updated successfully"
    ]);  
   

}


public function displayMyPieces(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $expertId = $expert->id;
    $pieces = Piece::
    with(['season', 'sub_category', 'master_category', 'usage'])
     ->whereHas('season', function($q){
         $q->select(['name', 'id']);
     })
     ->whereHas('sub_category', function($q){
         $q->select(['name', 'id']);
     })
     ->whereHas('master_category', function($q){
         $q->select(['name', 'id']);
     })
     ->whereHas('usage', function($q){
         $q->select(['name', 'id']);
     })
     ->where([
         ['type', 'expert'],
         ['expert_id', $expertId]
     ])
     ->get();


return response()->json($pieces);
}

public function addDetailsPieceExpert(Request $request, $pieces_id){
 
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $expertId = $expert->id;
    $pieceId = $pieces_id;

    $pieceDetails = new Piece_details();
    if($request['color_id']!=null)
    {
        $pieceDetails->color_id = $request['color_id'];
    }
    if($request['size_id']!=null){
        $pieceDetails->size_id =  $request['size_id'];
    }
    if($request['image']==null && $request['color_id']!=null){
        return response()->json([
                "status"=>false,
                "message"=>"the image should be uploaded if the color field is filled :)"
            ]);
    }
    if($request['image']!=null){
        $image_name = $this->saveImage($request['image'], 'storage/images/piecesImages/details');
        if($image_name->original['status']==false)
            return response()->json($image_name->original['message']);
        $pieceDetails->image = $image_name->original['message'];
    }
    $pieceDetails->pieces_id = $pieceId;
    $pieceDetails->save();

    return response()->json([
        "status"=>true,
        "message"=>"piece details added successfully"
    ]);

}
public function editDetailsPiece(Request $request,  $piece_detail_id){
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $expertId = $expert->id;

    $pieceDetails = Piece_details::with('color', 'size')->where('id', $piece_detail_id)->get();

    return response()->json($pieceDetails); 
}

public function updateDetailPiece(Request $request, $pieceDetails_id){
       
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $expertId = $expert->id;

    $pieceDetailsId = $pieceDetails_id;
    $updatedDetails = Piece_details::find($pieceDetailsId);

    if($request['color_id']!=null)
    $updatedDetails->color_id = $request['color_id'];

    if($request['size_id']!=null)
    $updatedDetails->size_id = $request['size_id'];

    if($request['image']==null && $request['color_id']!=null){
        return response()->json([
                "status"=>false,
                "message"=>"the image should be uploaded if the color field is filled :)"
            ]);
    }
    if($request['image']!=null){
        $image_name = $this->saveImage($request['image'], 'storage/images/piecesImages/details');
        if($image_name->original['status']==false)
            return response()->json($image_name->original['message']);
        $updatedDetails->image = $image_name->original['message'];
    }

    $updatedDetails->save();
    return response()->json([
        "status"=>true,
        "message"=>"piece details updated successfully"
    ]);

}


public function displayDetailedPiece(Request $request, $id){
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $expertId = $expert->id;
    $pieceId = $id;
    $piecesDetails= Piece::where('id','=',$id)->with(['sub_category','master_category','season','usage',
        'comment'=>function($q1){
            $q1->with('company','expert','table_user');
        }
        ,'piece_details' => function($q) use($id) {
            $q->with('color','size');
        }])->get();
    
    return response()->json($piecesDetails);
}

public function destroypiece(Request $request, $piece_id)
{   
    $pieces = Piece::find($piece_id);
    $pieceDetails = Piece_details::where('pieces_id', $piece_id);
    if(!$pieces){
    return response()->json([
        "status"=>false,
        "message"=>"Not found piece"
    ]);
    }
    $pieces->delete();
    $pieceDetails->delete();
    return response()->json([
        "status"=>true,
        "message"=>"piece deleted successfully"
    ]);
}


public function destroyDetailspiece(Request $request, $piece_detail_id)
{   
    $pieceDetailId = $piece_detail_id;
    $pieceDetails = Piece_details::where('id', $pieceDetailId);
    if(!$pieceDetails){
    return response()->json([
        "status"=>false,
        "message"=>"Not found piece"
    ]);
    }
    $pieceDetails->delete();
    return response()->json([
        "status"=>true,
        "message"=>"piece deleted successfully"
    ]);
}

public function sendMsgToUser(Request $request,$user_id){

    $validator = Validator::make($request->all(), 
    [ 
        "message" => "required|max:50"
            
    ]);  
    if ($validator->fails()) {  
        return response()->json(['status'=>false, 
        'message'=>$validator->errors()->all()
]);
    }
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $msg = new Msg();
    $msg->expert_id = $expert->id;
    $msg->user_id = $user_id;
    $msg->sender = 0;
    $msg->message = $request['message'];
    if($request['image']!=null){
        $image_name = $this->saveImage($request['image'], 'storage/images/collectionImages');
        if($image_name->original['status']==false)
            return response()->json($image_name->original['message']);
        $msg->image = $image_name->original['message'];
    }
    $msg->save();

     ################ Begin real time notification #######################
    //make new notification table to expert 
    $userNotification  = new User_notification();
    $userNotification->title = 'message';
    $userNotification->details = $expert->name.' messaged you';
    $userNotification->user_id = $user_id;
    $userNotification->save();
    //send the real time notification to pusher server
    $options = array(
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'encrypted' => true
        );
    $pusher = new Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'), 
                    $options
                );

    $data['title'] = 'message';
    $data['details'] = $userNotification->details;
    $data['user_id'] = $user_id;
    $data['expert_id'] = $expert->id;
    $pusher->trigger('expert-notification', 'App\\Events\\expertNotification', $data);

    ################ End real time notification #########################
    return response()->json([
    "status"=>true,
    "message"=>"message sended successfully"
    ]);

}

public function displayChatWithUser(Request $request, $user_id){
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $expertId = $expert->id;
    $userId = $user_id;
    $chats= Msg::where('is_seen', '=', '0')
    ->with('table_user')
    ->where([['user_id','=',$user_id],['expert_id','=',$expertId]])->get();
    
    foreach($chats as $chat){
        if($chat->sender == 0)
        {
            $chat->is_seen = 1;
            $chat->save();
        }
        
    }
    return response()->json($chats);
}


public function addComment(Request $request, $piece_id){
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $expertId = $expert->id;
    $comment = new Comment();
    $comment->expert_id = $expertId;
    $comment->admin_id = 0;
    $comment->piece_id = $piece_id;
    $comment->company_id = 0;
    $comment->user_id = 0;
    $comment->details = $request['details'];
    $comment->type = 'expert';
    $comment->save();

    return response()->json([
        "status"=>true,
        "message"=>"comment added successfully"
    ]);
}


public function CountPieces(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $expertId = $expert->id;
    $pieces = Piece::where('expert_id','=',$expertId)->get();
    $count=$pieces->COUNT('id');
    return response()->json($count);
}

public function getExpertData(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();

    $expertData = Expert::select('*')
    ->where('id', '=',$expert->id)
    ->get();

    return response()->json($expertData);
}

public function displayNotifications(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $expertId = $expert->id;

    $notifications = Expert_notification::where([
        ['expert_id', '=', $expertId],
        ['is_seen', '=', 0]
        ])->get();
    return response()->json($notifications);
}

public function readNotification(Request $request, $id){
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    $expertId = $expert->id;

    $readNotification = Expert_notification::find($id);
    $readNotification->is_seen = 1;
    $readNotification->save();
    return response()->json([
        "status"=>true,
        "message"=>$readNotification->title,
        "data"=>$readNotification
    ]);


}

public function getPredictedPieces(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();
    
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

public function sendEmailToExpert(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('expert-api')->attempt($credentials);
    $expert = Auth::guard('expert-api')->user();

    
    return response()->json(true);
}

}
