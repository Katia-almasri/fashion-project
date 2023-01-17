<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use Auth;
use Validator;
use Session;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Models\Table_user;
use App\Models\Usage;
use App\Models\Admin;
use App\Models\Form_user;
use App\Models\Form;
use App\Models\Company;
use App\Models\Like;
use App\Models\Piece;
use App\Models\Follow;
use App\Models\Expert;
use App\Models\Question;
use App\Models\Fashion_news;
use App\Models\Msg;
use App\Models\Comment;
use App\Models\Company_notification;
use App\Models\Expert_notification;
use App\Models\User_notification;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

class TableUserController extends Controller
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
             $token = auth()->guard('user-api')->attempt($credentials);
            if (!$token)
                return $this->returnError('E001','error in inputs');

            $user = Auth::guard('user-api')->user();
            $user->api_token = $token;
            //return token
            return $this->returnData('user', $user);

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }


    }

    public function register(Request $request)
      {
        
        $validator = Validator::make($request->all(), 
        [ 
            "email" => "required|email|max:255",
            "password" => "required|min:8|max:51",
            "gender"=>"required",
            "name"=>"required"
        
        ]);  
        if ($validator->fails()) {  
           //return response()->json($error = $validator->errors()->all());
            //return response()->json($validator->errors());
        return response()->json(['status'=>false, 
                                'message'=>$validator->errors()->all()
        ]);
        } 

        $user = new Table_user();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->image ='user.jpg';
        $user->password = bcrypt($request->password);
        ################# attampt #####################
        //1.search in user table
        $userInCompanies =Company::where('email', '=', $user->email)->first();
        $userInUser = Table_user::where('email', '=', $user->email)->first();
        $usertInExpert =Expert::where('email', '=', $user->email)->first();
        $userInAdmin = Admin::where('email', '=', $user->email)->first();
        if($userInCompanies==null && $userInUser==null && $usertInExpert==null && $userInAdmin==null){
            $user->save();
        return $this->login($request);
        return response()->json([
        'success' => true,
        'data' => $user
        ], Response::HTTP_OK);
        }
        else
        return response()->json(['status'=>false, 
        'message'=>"email already used"
]);
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

    public function getUserData(Request $request){
        $credentials = $request->only([]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();

        $userData = Table_user::select('*')
        ->where('id', '=', $user->id)
        ->get();
        return response()->json($userData);
    }
    
   

    public function displayUserProfile(Request $request){
        $credentials = $request->only(['id']);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        return response()->json(
            $user); 
    }

    public function editUserProfile(Request $request){
         
      
        $credentials = $request->only(['id']);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        return response()->json($user); 
    }

    public function updateUserProfile(Request $request){
        $validator = Validator::make($request->all(), 
        [ 
            "email" => "required|email|max:255",
            "password" => "min:8|max:51",
            "name"=>"required",
            "weight"=>"numeric|nullable",
            "length"=>"numeric|nullable"
            
        
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
            'weight',
            'length',
            'prefered_color',
            'prefered_style',
            'image',
            'password' 
        
        ]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        $user->name = $request['name'];
        $user->email = $request['email'];
        if($request['password']!=null && !Hash::check($request['password'],  $user->password)){
            $user->password = Hash::make($request['password']);
        }
        $user->date_of_birth = $request['date_of_birth'];
        $user->details = $request['details'];
        $user->weight = $request['weight'];
        $user->length = $request['length'];
        $user->prefered_color = $request['prefered_color'];
        $user->prefered_style = $request['prefered_style'];
        if($request['image']!=null)
        {
        $image_name = $this->saveImage($request['image'], 'storage/images/userImages');
        $user->image = $image_name;
        }
        $user->save();
        return response()->json([
            "status"=>true,
            "message"=>"updated successfully"
        ]);  
       

    }

  
    public function ratingForm(Request $request, $form_id){
        $validator = Validator::make($request->all(), 
        [ 
            "rate" => "numeric|required|max:5|min:1"   
        ]);  
        if ($validator->fails()) {  
            return response()->json(['status'=>false,
            'message'=>$validator->errors()->all()
        ]);
        } 
        $credentials = $request->only(['']);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();


        //check if user rated in the past
        $foundUser = Form_user::where([['user_id', '=', $user->id],['form_id','=',$form_id]])->first();
        if($foundUser!=null){
        return response()->json([
            "status"=>false,
            "message"=>"you have already rated!"
        ]);
      }
        else{
        $form_user = new Form_user();
        $form_user->form_id  =$form_id;
        $form_user->rate  =$request['rate'];
        $form_user->user_id = $user->id;
        $form_user->save();

        //change average rate in form
        $form = Form::find($form_id);
        $sum = DB::table('form_users')
        ->join('forms', 'form_users.form_id', 'forms.id')->where('form_id','=',$form_id)->sum('rate');

        $count = DB::table('form_users')
        ->join('forms', 'form_users.form_id', 'forms.id')
        ->where('form_id','=', $form_id)
        ->count('user_id');
        if($count!=0)
        $result = $sum/$count;
        $form->average_rate = $result;
        $form->save();
        return response()->json([
            "status"=>true,
            "message"=>"you just have rated!"
        ]);
        }
    }

    public function addPieceToFavorite(Request $request,$pieceId){
        $credentials = $request->only([]);
        $token = auth()->guard('user-api')->attempt($credentials);
        $user = Auth::guard('user-api')->user();
        $foundUser = Like::where([['user_id', '=', $user->id],['pieces_id','=',$pieceId]])->first();
        if($foundUser!=null){
            $piece = Piece::select('num_liked')->where('id', $pieceId)->increment('num_liked', -1);
            $foundUser->delete();
            return response()->json([
                "status"=>false,
                "message"=>"you disliked now"
            ]);
        }

       else{
        $like = new Like();
        $like->pieces_id = $pieceId;
        $like->user_id = $user->id;
        $like->save();
        $piece = Piece::select('*')->where('id', $pieceId)->get();
        $result =  Piece::select('num_liked', 'type', 'comany_id', 'expert_id')
        ->where('id','=',$pieceId)->increment('num_liked',1);
        
         
        return response()->json([
            "status"=>true,
            "message"=>"you liked now"
        ]);
        }
    }

   
public function displayMyFavorite(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('user-api')->attempt($credentials);
    $user = Auth::guard('user-api')->user();
    $userId = $user->id;
    $UserFavorite = DB::table('pieces')
    ->select('pieces.*',
        'sub_categories.name as sub_category_name',
        'master_categories.name as master_category_name',
        'usages.name as usage_name', 
        'seasons.name as season_name'
        )
    ->join('likes', 'pieces.id', '=', 'likes.pieces_id')
    ->join('sub_categories', 'pieces.sub_category_id', '=', 'sub_categories.id')
    ->join('master_categories', 'pieces.master_category_id', '=', 'master_categories.id')
    ->join('usages', 'pieces.usage_id', '=', 'usages.id')
    ->join('seasons', 'pieces.season_id', '=', 'seasons.id')
    ->where('likes.user_id','=',$userId)
    ->get();

    

     return response()->json($UserFavorite);
}


public function FollowToCompany(Request $request,$companyId){
    $credentials = $request->only([]);
    $token = auth()->guard('user-api')->attempt($credentials);
    $user = Auth::guard('user-api')->user();
    $foundUser = Follow::where([['user_id', '=', $user->id],['company_id','=',$companyId]])->first();
    if($foundUser!=null){
        $company = Company::select('num_followed')->where('id', $companyId)->increment('num_followed', -1);
        $foundUser->delete();
        return response()->json([
            "status"=>false,
            "message"=>"you unfollowed now"
        ]);
    }
   else{
        $follow = new Follow();
        $follow->company_id = $companyId;
        $follow->user_id = $user->id;
        $follow->save();
        $company = Company::find($companyId);
        $result =  Company::select('num_followed')
        ->where('id','=',$companyId)->increment('num_followed',1);
    ################ Begin real time notification #######################
    //make new notification table to expert 
        $companyNotification  = new Company_notification();
        $companyNotification->title = 'followed';
        $companyNotification->details = 'someone added you to followers list';
        $companyNotification->company_id = $company->id;
        $companyNotification->save();
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
        $data['title'] = $companyNotification->title;
        $data['details'] = $companyNotification->details;
        $data['company_id'] =  $company->id;
        $data['user_id'] = $user->id;
        $pusher->trigger('new-notification', 'App\\Events\\newNotification', $data);
    
    

        ################ End real time notification #########################

    return response()->json([
        "status"=>true,
        "message"=>"you followed now"
    ]);
    }
}

public function displayPiecesForCompanyFollowed(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('user-api')->attempt($credentials);
    $user = Auth::guard('user-api')->user();
    $userId = $user->id;
    $piecesForCompanyFollowed = DB::table('pieces')
    ->select('pieces.id','pieces.name','pieces.image')
    ->join('companies', 'companies.id', '=', 'pieces.company_id')
    ->join('follows', 'companies.id', '=', 'follows.company_id')
    ->where('user_id','=',$userId)
    ->get();
     return response()->json($piecesForCompanyFollowed);
}

public function getFollowedCompanies(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('user-api')->attempt($credentials);
    $user = Auth::guard('user-api')->user();

    $followedCompanies = Follow::with('company')
    ->where('user_id', $user->id)
    ->get();
    return response()->json($followedCompanies);
}


public function sendMsgToExpert(Request $request, $expert_id){
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
    $token = auth()->guard('user-api')->attempt($credentials);
    $user = Auth::guard('user-api')->user();

    $msg = new Msg();
    $msg->expert_id = $expert_id;
    $msg->user_id = $user->id ;
    $msg->message = $request['message'];
    $msg->sender = 1;
    if($request['image']!=null){
        $image_name = $this->saveImage($request['image'], 'storage/images/msgImages');
        if($image_name->original['status']==false)
            return response()->json($image_name->original['message']);
        $msg->image = $image_name->original['message'];
    }
    $msg->save();
    ################ Begin real time notification #######################
    //make new notification table to expert 
    $expertNotification  = new Expert_notification();
    $expertNotification->title = 'message';
    $expertNotification->details = $user->name.' messaged you';
    $expertNotification->expert_id = $expert_id;
    $expertNotification->save();
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
    $data['details'] = $expertNotification->details;
    $data['user_id'] = $user->id;
    $data['expert_id'] = $expert_id;
    $pusher->trigger('new-notification', 'App\\Events\\newNotification', $data);

    ################ End real time notification #########################

  return response()->json([
    "status"=>true,
    "message"=>"msg sended successfully"
   ]);

}

public function displayChatWithExpert(Request $request, $expert_id){
    $credentials = $request->only([]);
    $token = auth()->guard('user-api')->attempt($credentials);
    $user = Auth::guard('user-api')->user();
    $user_id = $user->id;
    $expertId = $expert_id;
    $chats= Msg::where('is_seen', '=', '0')
    ->with('expert')
    ->where([['user_id','=',$user_id],['expert_id','=',$expertId]])->get();

   foreach($chats as $chat){
    if($chat->sender == 1)
        {
            $chat->is_seen = 1;
            $chat->save();
        }
   }
   
    return response()->json($chats);
}

public function displayChatsUser(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('user-api')->attempt($credentials);
    $user = Auth::guard('user-api')->user();
    $user_id = $user->id;
    $chat=DB::table('experts')
    ->join('msg', 'experts.id', 'msg.expert_id')->where('msg.user_id','=',$user_id)->select('experts.id','experts.name','experts.image')->get();
    
    return response()->json($chat);
}


public function addComment(Request $request, $piece_id){
    $credentials = $request->only([]);
    $token = auth()->guard('user-api')->attempt($credentials);
    $user = Auth::guard('user-api')->user();

    $userId = $user->id;
    $comment = new Comment();
    $comment->user_id = $userId;
    $comment->admin_id = 0;
    $comment->piece_id = $piece_id;
    $comment->expert_id = 0;
    $comment->company_id = 0;
    $comment->details = $request['details'];
    $comment->type = 'user';
    $comment->save();

    return response()->json([
        "status"=>true,
        "message"=>"comment added successfully"
    ]);
}


}
