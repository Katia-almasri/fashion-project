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
use App\Models\Admin;
use App\Models\Usage;
use App\Models\Company;
use App\Models\Table_user;
use App\Models\Expert;
use App\Models\Fashion_news;
use App\Models\Piece;
use App\Models\Piece_details;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Pusher\Pusher;
use App\Models\Company_notification;
use App\Mail\welcomeMail;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
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
             $token =   
            if (!$token)
                return $this->returnError('E001','error in inputs');

            $admin = Auth::guard('admin-api')->user();
            $admin->api_token = $token;
            //return token
            return $this->returnData('admin', $admin);

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
        ]);  
        if ($validator->fails()) {  
        return response()->json(['error'=>$validator->errors()], 401); 
        } 

        $admin = new Admin();
        $admin->email = $request->email;
        $admin->password = bcrypt($request->password);

         //////////////////////attampt //////////////////////////
        //1.search in admin table
        $adminInCompanies =Company::where('email', '=', $admin->email)->first();
        $adminInUser = Table_user::where('email', '=', $admin->email)->first();
        $adminInExpert =Expert::where('email', '=', $admin->email)->first();
        $adminInAdmin = Admin::where('email', '=', $admin->email)->first();
        if($adminInCompanies==null && $adminInUser==null && $adminInExpert==null && $adminInAdmin==null){
        $admin->save();
        return $this->login($request);
        return response()->json([
        'success' => true,
        'data' => $user
        ], Response::HTTP_OK);
     }
        else
            return  $this -> returnError('','some thing went error');
        ////////////////////end attempt ///////////////////////////
        
}

public function logout(Request $request)
    {
    $token = $request -> header('auth-token');
    if($token){
        try {
                JWTAuth::setToken($token)->invalidate(); //logout
            }
            catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                return  $this -> returnError('','some thing went wrongs');
            }
            return $this->returnSuccessMessage('Logged out successfully');
        }
        else
        {
            $this -> returnError('','some thing went wrongs');
        }

}
    
  
public function displayCompanies(Request $request){
        $credentials = $request->only([]);
        $token = auth()->guard('admin-api')->attempt($credentials);
        $admin = Auth::guard('admin-api')->user();
        $companies = Company::get();
        return response()->json($companies);
}

public function displayExperts(Request $request){
        $credentials = $request->only([]);
        $token = auth()->guard('admin-api')->attempt($credentials);
        $admin = Auth::guard('admin-api')->user();
        $adminId = $admin->id;
           $experts = Expert::get();
  
    return response()->json($experts);
}

public function addAdminFashionNews(Request $request){
        $validator = Validator::make($request->all(), 
        [ 
            "details" => "required|max:255",
            "image" => "required",   
        ]);  
        if ($validator->fails()) {  
        return response()->json(['error'=>$validator->errors()], 401); 
        } 
        

        $credentials = $request->only([]);
        $token = auth()->guard('admin-api')->attempt($credentials);
        $admin = Auth::guard('admin-api')->user();

        //input the details
        $fashionNews = new Fashion_news();
        $fashionNews->details = $request->details;
        $image_name = $this->saveImage($request['image'], 'storage/images/fashionNewsImages');
        if($image_name->original['status']==false)
            return response()->json($image_name->original['message']);
        $fashionNews->image = $image_name->original['message'];
        
        $fashionNews->type = "admin";
        $fashionNews->expert_id = 0;
        $fashionNews->admin_id = $admin->id;
        $fashionNews->company_id = 0;
        $fashionNews->save();

        return response()->json([
            "status"=>true,
            "message"=>"fashion news added successfully"
        ]);

}

public function displayMyNews_Admin(Request $request){
        $credentials = $request->only([]);
        $token = auth()->guard('admin-api')->attempt($credentials);
        $admin = Auth::guard('admin-api')->user();
        $adminId = $admin->id;
           $news = Fashion_news::where('admin_id','=',$adminId)->get();
  
    return response()->json($news);
}

public function deleteMyNews_Admin(Request $request, $news_id)
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
        "message"=>"news deleted successfully"
    ]);
    }
}

public function addAnotherAdmin(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
        "email" => "required|email|max:255",
        "password" => "required|min:8|max:51",
    ]);  
    if ($validator->fails()) {  
    return response()->json(['error'=>$validator->errors()], 401); 
    } 
    

    $credentials = $request->only([]);
    $token = auth()->guard('admin-api')->attempt($credentials);
    $admin = Auth::guard('admin-api')->user();

    //input the details
    $admin = new Admin();
    $admin->email = $request->email;
    $admin->password = bcrypt($request->password);
    $admin->image = 'admin.jpg';
    $admin->name = $request->name;
    $adminInCompanies =Company::where('email', '=', $admin->email)->first();
    $adminInUser = Table_user::where('email', '=', $admin->email)->first();
    $adminInExpert =Expert::where('email', '=', $admin->email)->first();
    $adminInAdmin = Admin::where('email', '=', $admin->email)->first();
    if($adminInCompanies==null && $adminInUser==null && $adminInExpert==null && $adminInAdmin==null)
    {
        $admin->save();
        return response()->json([
        "status"=>true,
        "message"=>" added Admin successfully"
        ]);
    }
    else{

        return response()->json([
            "status"=>false,
            "message"=>"email already used"
        ]);
    }

}

public function blockcompany($company_id)
    {

    $company = Company::find($company_id);
    $email = $company->email;
    $company->delete();
    Mail::to($email)->send(new welcomeMail());
    return response()->json([
        "status"=>true,
        "message"=>"company blocked"
    ]);
}

public function cancelBlockCompany($company_id)
    {
    $company = Company::onlyTrashed()->where('id' , $company_id)->first()->restore() ;   
    return response()->json([
        "status"=>true,
        "message"=>"cancel block for company"
    ]);
}

public function blockexpert($expert_id)
    {
    
    $expert = Expert::find($expert_id);
    $email = $expert->email;
    $expert->delete();
    Mail::to($email)->send(new welcomeMail());
    return response()->json([
        "status"=>true,
        "message"=>"expert blocked"
    ]);
}

public function cancelBlockexpert($expert_id)
    {
    $expert = Expert::onlyTrashed()->where('id' , $expert_id)->first()->restore() ;   
    return response()->json([
        "status"=>true,
        "message"=>"cancel block for expert"
    ]);
}

public function displayAllBlockedCompanies(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('admin-api')->attempt($credentials);
    $admin = Auth::guard('admin-api')->user();

    $blockedCompanies = Company::onlyTrashed()->get();
    return response()->json($blockedCompanies);
}

public function displayAllBlockedExperts(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('admin-api')->attempt($credentials);
    $admin = Auth::guard('admin-api')->user();

    $blockedExperts = Expert::onlyTrashed()->get();
    return response()->json($blockedExperts);
}

public function CountNotBlockedCompany_Admin(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('admin-api')->attempt($credentials);
    $admin = Auth::guard('admin-api')->user();
    $adminId = $admin->id;

    $Company = Company::where('deleted_at', '=', null)
    ->get();
    $count=$Company->COUNT('id');
    return response()->json($count);
}


public function CountBlockedCompany(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('admin-api')->attempt($credentials);
    $admin = Auth::guard('admin-api')->user();
    $adminId = $admin->id;
   
    $company = Company::onlyTrashed()->get();
    $count=$company->COUNT('id');
    return response()->json($count);
}

public function CountNotBlockedExpert(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('admin-api')->attempt($credentials);
    $admin = Auth::guard('admin-api')->user();
    $adminId = $admin->id;
    $expert = Expert::where('deleted_at', '=', null)
    ->get();
    $count=$expert->COUNT('id');
    return response()->json($count);
}

public function CountBlockedExpert(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('admin-api')->attempt($credentials);
    $admin = Auth::guard('admin-api')->user();
    $adminId = $admin->id;
    $expert = Expert::onlyTrashed()->get();
    $count=$expert->COUNT('id');
    return response()->json($count);
}

public function CountUser(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('admin-api')->attempt($credentials);
    $admin = Auth::guard('admin-api')->user();
    $adminId = $admin->id;
    $user = Table_user::get();
    $count=$user->COUNT('id');
    return response()->json($count);
}

public function CountPieces(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('admin-api')->attempt($credentials);
    $admin = Auth::guard('admin-api')->user();
    $adminId = $admin->id;
    $pieces = Piece::get();
    $count=$pieces->COUNT('id');
    return response()->json($count);
}


public function getAdminData(Request $request){
    $credentials = $request->only([]);
    $token = auth()->guard('admin-api')->attempt($credentials);
    $admin = Auth::guard('admin-api')->user();
    $adminId = $admin->id;

    $adminData = Admin::select('*')
    ->where('id', '=', $adminId)
    ->get();
    return response()->json($adminData);
}
####################### Begin csv controls ###################################
public function exportToCSV(Request $request, $seasonName){

   $year = Carbon::now()->format('Y');
   $filePath = 'storage\\csvFiles';
   $fileName = $filePath.'\\'.$year.'_'.$seasonName.'.csv';
   if (!file_exists($fileName)) {
    touch($fileName);
}
   $tasks = Piece_details::where('color_id', '!=', null)->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('color_id', 'created_at');

        $callback = function() use($tasks, $columns, $fileName) {
            $file = fopen($fileName, 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['color_id']  = $task->color_id;
                $row['created_at']    = $task->created_at;
               
                fputcsv($file, array($row['color_id'], $row['created_at']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

        /////////////////////////////////////////////////////////
    
        
    }
    
    ################## End Real time notification #######################
        
    

    public function createCSVFile(Request $request, $fileName){

        $process = new Process(["python", "storage\\pythonScripts\\python_script.py", $fileName]);
        $process->setTimeout(4600);
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $result = $process->getOutput();
        if(str_contains($result, 'predictions has been inserted successfully')){
        ################## Begin Real time notification #######################
            //make new notification to companies
            //1. get all companies
            $companies = Company::get();
            if(count($companies)>0){
            //1. send notification to all of the companies
                foreach ($companies as $company) {
                    $companyNotification = new Company_notification();
                    $companyNotification->company_id = $company->id;
                    $companyNotification->title = 'fashion prediction';
                    $companyNotification->details = 'seasonality color prediction has been ready now';
                    $companyNotification->save();
                            
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

                $data['company_id'] = $companyNotification->company_id;
                $data['title'] = 'fashion prediction';
                $data['details'] = 'seasonality color prediction has been ready now';
                $pusher->trigger('company-notification', 'App\\Events\\companyNotification', $data);
        }
    }
    return response()->json($result);
    
}


}




