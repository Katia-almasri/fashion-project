<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\adminRequest;  
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
use App\Models\Piece_details    ;
use App\Models\pieceDetails_Collection;
use App\Models\Collection;
use App\Models\Form;
use App\Models\Form_user;
use App\Models\User_notification;
use App\Models\Follow;
use App\Models\Comment;
use App\Models\CartCollection;
use App\Models\Predicted;
use App\Models\Company_notification;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class CompanyController extends Controller
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
             $token = auth()->guard('company-api')->attempt($credentials);
            if (!$token)
                return $this->returnError('E001', 'error in inputs');
            $company = Auth::guard('company-api')->user();
            $company->api_token = $token;
            //return token
            return $this->returnData('company', $company);

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }


}

public function register(Request $request)
    { 
        $validator = Validator::make($request->all(), 
        [ 
            "email" => "required|email|max:255",
            "password" => "required|min:8|max:15",
            "name"=>"required:min:3|max:255",
            "location"=>"required|max:255",
            "date_of_establishment"=>"required|date",
            "major_category"=>"required",
            "details"=>"max:255"
        ]);  
        if ($validator->fails()) {  
            return response()->json(['status'=>false, 
            'message'=>$validator->errors()->all()
        ]);
        } 
        
        $company = new Company();
        $company->email = $request->email;
        $company->name = $request->name;
        $company->password =bcrypt($request->password);
        $company->date_of_establishment = $request->date_of_establishment;
        $company->location = $request->location;
        $company->major_category = $request->major_category;
        $company->image = 'user.jpg';
        if($request->details!=null)
        $company->details = $request->details;
        //////////////////////attampt //////////////////////////
        //1.search in company table
        $companyInCompanies =Company::where('email', '=', $company->email)->first();
        $companyInUser = Table_user::where('email', '=', $company->email)->first();
        $comanyInExpert =Expert::where('email', '=', $company->email)->first();
        $companyInAdmin = Admin::where('email', '=', $company->email)->first();
        if($companyInCompanies==null && $companyInUser==null && $comanyInExpert==null && $companyInAdmin==null){
                //if not exist in any table then add it to company table
                $company->save();
                //if ($this->token) {
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

public function displayCompanyProfile(Request $request){
        $credentials = $request->only(['id']);
        $token = auth()->guard('company-api')->attempt($credentials);
        $company = Auth::guard('company-api')->user();
        return response()->json($company); 
}

public function editCompanyProfile(Request $request){
         
      
        $credentials = $request->only(['id']);
        $token = auth()->guard('company-api')->attempt($credentials);
        $company = Auth::guard('company-api')->user();
        return response()->json($company); 
}

public function updateCompanyProfile(Request $request){
        $validator = Validator::make($request->all(), 
        [ 
            "email" => "required|email|max:255",
            "new_password" => "min:3|max:51",
            "name"=>"required:min:3|max:255",
            "details"=>"max:255",
            "location"=>"required|max:255",
            "date_of_establishment"=>"required|date",
        
        ]);  
        if ($validator->fails()) {  
            return response()->json(['status'=>false, 
            'message'=>$validator->errors()->all()
        ]);
        }
        
        $credentials = $request->only([
            'name',
            'email',
            'location',
            'major_category',
            'date_of_establishment',
            'image',
            'details',
            'password' 
        
        ]);
        
        $token = auth()->guard('company-api')->attempt($credentials);
        $company = Auth::guard('company-api')->user();
        $company->name = $request['name'];

        //search for emails in all tables
        $companyInCompanies =Company::where([['email', '=', $company->email],['id','!=',$company->id]])->first();
        $companyInUser = Table_user::where('email', '=', $company->email)->first();
        $comanyInExpert =Expert::where('email', '=', $company->email)->first();
        $companyInAdmin = Admin::where('email', '=', $company->email)->first();
        if($companyInCompanies==null && $companyInUser==null && $comanyInExpert==null && $companyInAdmin==null){
        $company->email = $request['email'];
        }
        else{
        return response()->json([
            "status"=>false,
            "message"=>"email Already Used"
        ]);  
        }
        if($request['details']!=null)
            $company->details = $request['details'];
        if($request['password']!=null && !Hash::check($request['password'],  $company->password)){
            $company->password = Hash::make($request['password']);
        }
        $company->location = $request['location'];
        $company->major_category = $request['major_category'];
        $company->date_of_establishment = $request['date_of_establishment'];
        if($request['image']!=null){
            $image_name = $this->saveImage($request['image'], 'storage/images/companyImages');
            if($image_name->original['status']==false)
                return response()->json($image_name->original['message']);
            $company->image = $image_name->original['message'];
        }
               
        $company->save();

        return response()->json([
            "status"=>true,
            "message"=>"updated successfully"
        ]);  
       

}

public function addFashionNews(Request $request){
        $validator = Validator::make($request->all(), 
        [ 
            "details" => "required|max:255",
            "image" => "required"
        ]);  
        if ($validator->fails()) {  
            return response()->json(['status'=>false, 
            'message'=>$validator->errors()->all()
        ]);
        } 
        

        $credentials = $request->only([]);
        $token = auth()->guard('company-api')->attempt($credentials);
        $company = Auth::guard('company-api')->user();

        //input the details
        $fashionNews = new Fashion_news();
        $fashionNews->details = $request->details;
        $fashionNews->type = "company";
        $fashionNews->company_id = $company->id;
        $fashionNews->admin_id = 0;
        $fashionNews->expert_id = 0;
        
        $image_name = $this->saveImage($request['image'], 'storage/images/fashionNewsImages');
        if($image_name->original['status']==false)
            return response()->json($image_name->original['message']);
        $fashionNews->image = $image_name->original['message'];

        $fashionNews->save();

        return response()->json([
            "status"=>true,
            "message"=>"fashion news added successfully"
        ]);
}

    public function getUsage(){
        $usages = Usage::get();
        return response()->json($usages);
    }

    public function getSeasons(){
        $seasons = Season::get();
        return response()->json($seasons);
    }

    public function getSubCategories(){
        $subCategories = Sub_category::get();
        return response()->json($subCategories);
    }

    public function getMasterCategories(){
        $masterCategories = Master_category::get();
        return response()->json($masterCategories);
    }

    public function getColor(){
        $colors = Color::get();
        return response()->json($colors);
    }

    public function getSize(){
        $sizes = Size::get();
        return response()->json($sizes);
    }

    public function getCompanyData(Request $request){
        $credentials = $request->only([]);
        $token = auth()->guard('company-api')->attempt($credentials);
        $company = Auth::guard('company-api')->user();

        $companyData = Company::select('*')
        ->where('id', '=', $company->id)
        ->get();
        return response()->json($companyData);
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
        $token = auth()->guard('company-api')->attempt($credentials);
        $company = Auth::guard('company-api')->user();
        $piece = new Piece();
        $piece->name = $request['name'];
        if($request['price']!=null)
            $piece->price = $request['price'];
        $piece->type = "company";
        $piece->company_id = $company->id;
        $piece->expert_id = 0;
        $piece->sub_category_id =  $request['sub_category_id'];
        $piece->master_category_id =  $request['master_category_id'];
        $piece->season_id =$request['season_id'];
        $piece->usage_id = $request['usage_id'];

        $image_name = $this->saveImage($request['originalImage'], 'storage/images/piecesImages');
        if($image_name->original['status']==false)
             return response()->json($image_name->original['message']);
        $piece->image = $image_name->original['message'];
       
        $piece->save();

        //add details
        $pieceDetails = new Piece_details();
        if($request['color_id']!=null)
            $pieceDetails->color_id =  $request['color_id'];
        if($request['size_id']!=null)
        $pieceDetails->size_id =  $request['size_id'];

        if($request['coloredImage']==null && $request['color_id']!=null){
            return response()->json([
                    "status"=>false,
                    "message"=>"the image should be uploaded if the color field is filled :)"
                ]);
        }

        if($request['coloredImage']!=null){
            $image_name = $this->saveImage($request['coloredImage'], 'storage/images/piecesImages/details');
            if($image_name->original['status']==false)
                return response()->json($image_name->original['message']);
            $pieceDetails->image = $image_name->original['message'];
        }
            
        $piece->piece_details()->save($pieceDetails);

    ################## Begin Real time notification #######################
    //make new notification to users follow this company
    //1. know the followers(from followers table)
    $followers = Follow::where('company_id', '=', $company->id)
    ->with('table_user')
    ->get();
    //2. send notification to all of the users
    if(count($followers)>0){
        foreach ($followers as $follower) {
            $userNotification = new User_notification();
            $userNotification->user_id = $follower->table_user->id;
            $userNotification->title = 'new pieces';
            $userNotification->save();
                      
        }
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

        $data['user_id'] = $userNotification->user_id;
        $data['title'] = 'new piece';
        $data['details'] = $userNotification->details;
        $pusher->trigger('company-notification', 'App\\Events\\companyNotification', $data);
    }
    ################## End Real time notification #######################
        return response()->json([
            "status"=>true,
            "message"=>"piece added successfully"
        ]);
}

public function displayDetailedPiece(Request $request, $id){
        $credentials = $request->only([]);
        $token = auth()->guard('company-api')->attempt($credentials);
        $company = Auth::guard('company-api')->user();
        $companyId = $company->id;
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

public function displayPieces(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();
    $pieces = Piece::
    where('company_id', '=', $company->id)
    ->with('like', 'usage', 'season', 'sub_category', 'master_category','company','expert')
    ->whereHas('expert',function($query) {
    $query->where('experts.deleted_at','=',NULL);
    })
    ->orwhereHas('company',function($query) {
    $query->where('companies.deleted_at','=',NULL);
    })
    ->get();
    return response()->json($pieces);

}


   

    public function addDetailsPiece(Request $request, $pieces_id){
 
        $credentials = $request->only([]);
        $token = auth()->guard('company-api')->attempt($credentials);
        $company = Auth::guard('company-api')->user();
        $companyId = $company->id;
        $pieceId = $pieces_id;

        $pieceDetails = new Piece_details();
        if($request['color_id']!=null)
            $pieceDetails->color_id = $request['color_id'];

        if($request['size_id']!=null)
            $pieceDetails->size_id =  $request['size_id'];

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

    public function editPiece(Request $request, $piece_id){
        $credentials = $request->only([]);
        $token = auth()->guard('company-api')->attempt($credentials);
        $company = Auth::guard('company-api')->user();
        $companyId = $company->id;
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
            ['type', 'company'],
            ['company_id', $companyId],
            ['id', $pieceId]
        ])
        ->get();

        return response()->json($piece); 
    }

    public function editDetailsPiece(Request $request,  $piece_detail_id){
        $credentials = $request->only([]);
        $token = auth()->guard('company-api')->attempt($credentials);
        $company = Auth::guard('company-api')->user();
        $companyId = $company->id;

        $pieceDetails = Piece_details::with('color', 'size')->where('id', $piece_detail_id)->get();

        return response()->json($pieceDetails); 
    }

    public function updatePiece(Request $request , $piece_id){
        $validator = Validator::make($request->all(), 
        [ 
            "name"=>"required|min:3|max:255",
            "image"=>"required",
            "price"=>"nullable|min:0"
        
        ]);  
        if ($validator->fails()) {  
            return response()->json(['status'=>false, 
            'message'=>$validator->errors()->all()
        ]);
        }


        $credentials = $request->only([]);
        $token = auth()->guard('company-api')->attempt($credentials);
        $company = Auth::guard('company-api')->user();

        $piece = Piece::find($piece_id);

        if($request['name']!=null)
             $piece->name = $request['name'];

        if($request['usage_id']!=null)
            $piece->usage_id = $request['usage_id'];

        if($request['image']==null && $request['color_id']!=null){
        return response()->json([
                "status"=>false,
                "message"=>"the image should be uploaded if the color field is filled :)"
            ]);
        }

        if($request['image']!=null){
            $image_name = $this->saveImage($request['image'], 'storage/images/piecesImages');
            if($image_name->original['status']==false)
                return response()->json($image_name->original['message']);
            $piece->image = $image_name->original['message'];
        }
        
       
        
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

    public function updateDetailPiece(Request $request, $pieceDetails_id){
       
        $credentials = $request->only([]);
        $token = auth()->guard('company-api')->attempt($credentials);
        $company = Auth::guard('company-api')->user();

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

   
//details inside register?
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



public function displayMyNewsCompany(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();
    $companyId = $company->id;
       $news = Fashion_news::where('company_id','=',$companyId)->get();

    return response()->json($news);
}

public function deleteMyNewscompany(Request $request, $news_id)
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


public function addComment(Request $request, $piece_id){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();

    $companyId = $company->id;
    $comment = new Comment();
    $comment->company_id = $companyId;
    $comment->admin_id = 0;
    $comment->piece_id = $piece_id;
    $comment->expert_id = 0;
    $comment->user_id = 0;
    $comment->details = $request['details'];
    $comment->type = 'company';
    $comment->save();

    return response()->json([
        "status"=>true,
        "message"=>"comment added successfully"
    ]);


}


public function addForm(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
        //year format
        "year"=>"required|numeric",
        "name_form"=>"required|max:50",
        "season_id"=>"required|numeric"
    ]);  
    if ($validator->fails()) {  
        return response()->json(['status'=>false, 
        'message'=>$validator->errors()->all()
    ]);
    }
    
    
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();
    $form = new Form();
    $companyId = $company->id;
    $form->company_id = $companyId;
    $form->name_form = $request['name_form'];
    $form->season_id = $request['season_id'];
    $current_year=date('Y');
    
    $form->year = $request['year'];
    if($form->year < $current_year)
        return response()->json([
            "status"=>false,
            "message"=>"Enter the current year"
        ]);
    
    if($request['details']!=null)
        $form->details = $request['details'];
    $form->average_rate = 0;
    
    $form->save();

     ################## Begin Real time notification #######################
    //make new notification to users follow this company
    //1. know the followers(from followers table)
    $followers = Follow::where('company_id', '=', $company->id)
    ->with('table_user')
    ->get();
    //2. send notification to all of the users
    if(count($followers)>0){
        foreach ($followers as $follower) {
            $userNotification = new User_notification();
            $userNotification->user_id = $follower->table_user->id;
            $userNotification->title = 'new form';
            $userNotification->details = $company->name.' has added new form';
            $userNotification->save();
                      
        }
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

        $data['user_id'] = $userNotification->user_id;
        $data['title'] = 'new form';
        $data['details'] = $userNotification->details;
        $pusher->trigger('company-notification', 'App\\Events\\companyNotification', $data);
    }
    ################## End Real time notification #######################
    return response()->json([
        "status"=>true,
        "message"=>"form added successfully"
    ]);
}

public function displayForms(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();
    $companyId = $company->id;

    $forms = Form::where('company_id', $companyId)->with('season')->get();
    return response()->json([
        "data"=>$forms
        ]);

}

public function destroyForm(Request $request, $form_id)
    {   
    $forms = Form::find($form_id);
    $formUser = Form_user::where('form_id', $form_id);
    if(!$forms){
    return response()->json([
        "status"=>false,
        "message"=>"Not found form"
    ]);
    }
    $forms->delete();
    $formUser->delete();
    return response()->json([
        "status"=>true,
        "message"=>"form deleted successfully"
    ]);
}


public function addDetailPieceToCollection(Request $request, $piece_detail_id, $type){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();

    $cartCollection = new CartCollection();
    $cartCollection->company_id = $company->id;
    if($type!=null && $type=='piece_details'){
        $cartCollection->type = 'piece_details';
        $cartCollection->piece_details_id = $piece_detail_id;
        $cartCollection->piece_id =0;
    }
    else if($type!=null &&  $type=='piece'){
        $cartCollection->type = 'piece';
        $cartCollection->piece_id = $piece_detail_id;
        $cartCollection->piece_details_id = 0;
    }
        

    $cartCollection->save();
    return response()->json([
        "status"=>true,
        "message"=>"added to cart"
    ]);

}

public function desplayAllPiecesAndPiecesDetail(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();

    $piecesAndPiecesDetails = Piece::with(['piece_details'=>function($q){
        $q->where('color_id', '!=', null);
    }])
    ->where('company_id', '=', $company->id)
    ->get();
    
    return response()->json($piecesAndPiecesDetails);
}

public function displayCollectionCart(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();
    $companyId=$company->id;

    $cartCollection = cartCollection::with('pieceDetails', 'pieces')
    ->where('company_id',$companyId)
    ->get();

   return response()->json($cartCollection);
}



public function displayCollection(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();
    $companyId=$company->id;
    $Collection = Collection::where( 'company_id',$companyId)->get();
    return response()->json($Collection);
}

public function displayDetailedCollection(Request $request, $id){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();

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


public function deleteDetailPieceFromCollectionCart(Request $request, $collection_cart_id){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();
  
        $collectionCart = CartCollection::where([
            ['company_id', $company->id],
            ['id', $collection_cart_id]
        ])->first();
        if($collectionCart==null){
            return response()->json([
                "status"=>false,
                "message"=>"piece detail not found in this collection"
            ]);
    }
   
    
    

    $collectionCart->delete();
    return response()->json([
        "status"=>true,
        "message"=>"piece detail deleted from collection cart successfully"
    ]);
}
public function confirmPieccesCollection(Request $request)
{
    $validator = Validator::make($request->all(), 
    [ 
        "collection_name"=>"required|min:3|max:255",
    
    ]);  
    if ($validator->fails()) {  
        return response()->json(['status'=>false, 
        'message'=>$validator->errors()->all()
    ]);
    }
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();

    $pieceDetails = new pieceDetails_Collection();
    $confirmations = [];
    //before make new collection if number of piece <=1 then
    // company should add piece to make collection
    $cartCollection = CartCollection::where([['company_id', $company->id]])->get();
    if( count($cartCollection)<=1){
        return response()->json([
            "status"=>false,
            "message"=>"add another piece to make a collection"
        ]);
    }
    //make new collection
    $collection = new Collection();
    $collection->name = $request['collection_name'];
    if($request['collection_image']!=null){
        $image_name = $this->saveImage($request['collection_image'], 'sotrage/images/collectionImages');
        if($image_name->original['status']==false)
            return response()->json($image_name->original['message']);
        $collection->image = $image_name->original['message'];
    }
    $collection->company_id = $company->id;
    $collection->save();
    $collectionId = $collection->id;
   
    for ( $i = 0; $i < count($cartCollection); $i++) {  
            $confirmations[] = [
                'type' => $cartCollection[$i]['type'],
                'collection_id'=>$collectionId,
                'piece_id' => $cartCollection[$i]['piece_id'],
                'pieceDetails_id' => $cartCollection[$i]['piece_details_id']
            ];
    }
    $deleteCart = CartCollection::where([['company_id', $company->id]])->delete();
    $pieceDetails::insert($confirmations);

    ################## Begin Real tome notification #######################
    //make new notification to users follow this company
    //1. know the followers(from followers table)
    $followers = Follow::where('company_id', '=', $company->id)
    ->with('table_user')
    ->get();
    //2. send notification to all of the users
    if(count($followers)>0){
        foreach ($followers as $follower) {
            $userNotification = new User_notification();
            $userNotification->user_id = $follower->table_user->id;
            $userNotification->title = 'new collections';
            $userNotification->details = $company->name.' has added new collection';
            $userNotification->save();
                      
        }
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

        $data['user_id'] = $userNotification->user_id;
        $data['title'] = 'new collections';
        $data['details'] = $userNotification->details;
        $pusher->trigger('company-notification', 'App\\Events\\companyNotification', $data);
    }
    ################## End Real tome notification #######################
    return response()->json([
        "status"=>true,
        "message"=>"new collection added successfully"
    ]);
}  

public function destroyCollection(Request $request, $collection_id)


    {   
    $collection = Collection::find($collection_id);
    $pieceDetailsCollection = pieceDetails_Collection::where('collection_id', $collection_id);
    if(!$collection){
    return response()->json([
        "status"=>false,
        "message"=>"Not found collection"
    ]);
    }
    $collection->delete();
    $pieceDetailsCollection->delete();
    return response()->json([
        "status"=>true,
        "message"=>"collection deleted successfully"
    ]);
}

public function CountFollowers(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();
    $companyId = $company->id;
    $follow = Company::where('id', $companyId)
    ->select('num_followed')->get();
    return response()->json($follow);
}

public function CountPieces(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();
    $companyId = $company->id;
    $pieces = Piece::where('company_id','=',$companyId)->get();
    $count=$pieces->COUNT('id');
    return response()->json($count);
}

public function displayNotifications(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();
    $companyId = $company->id;

    $notifications = Company_notification::where([
        ['company_id', '=', $companyId],
        ['is_seen', '=', 0]
        ])->get();
    $count = $notifications->count('id');
     return response()->json([
        "status"=>true,
        "notifications"=>$notifications,
        "count"=>$count
    ]);
}

public function readNotification(Request $request, $id){
    $credentials = $request->only([]);
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();
    $companyId = $company->id;

    $readNotification = Company_notification::find($id);
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
    $token = auth()->guard('company-api')->attempt($credentials);
    $company = Auth::guard('company-api')->user();
    
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

public function index(Request $request)
{
    $pieces = Piece::limit(30)->get();

    $data = '';
    if ($request->ajax()) {
        foreach ($pieces as $piece) {
            $data.='<li>'.$piece->name.' <strong>'.$piece->usage_id.'</strong> : '.$piece->season_id.'</li>';
        }
        return $data;
    }
    return view('home', compact('pieces'));
}

public function test(){
    //return (base_path("public\\storage\\images\\piecesImages\\details\\1658507525.jpg"));
    return response()->file(base_path("public\\storage\\images\\piecesImages\\details\\1658507525.jpg"));
}

}   
