<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use App\Http\Requests\adminRequest;  //
use Auth;
use Validator;
use Session;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usage;
use App\Models\Expert;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Table_user;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use GeneralTrait;
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {

        try{
            $rules = [
                "email" => "required|email|max:255",
                "password" => "required|min:3|max:51"

            ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($validator, $code);
        }

        $credentials = $request->only(['email', 'password']);
        $token1 = auth()->guard('user-api')->attempt($credentials);
        $token2 = auth()->guard('admin-api')->attempt($credentials);
        $token3 = auth()->guard('expert-api')->attempt($credentials);
        $token4 = auth()->guard('company-api')->attempt($credentials);
        if($token1)
            {
                $user = Auth::guard('user-api')->user();
                $user->api_token = $token1;
                //return token
                return $this->returnData('user', $user);
            }
        if($token2)
        {
            $admin = Auth::guard('admin-api')->user();
            $admin->api_token = $token2;
            //return token
            return $this->returnData('admin', $admin);
        }
        if($token3)
            {
                $expert = Auth::guard('expert-api')->user();
                $expert->api_token = $token3;
                //return token
                return $this->returnData('expert', $expert);
            }
        if($token4)
        {
            $company = Auth::guard('company-api')->user();
            $company->api_token = $token4;
            //return token
            return $this->returnData('company', $company);
        }
        else
            return  $this -> returnError('','invalid inputs');
    }catch(\Exception $ex) {
        return $this->returnError($ex->getCode(), $ex->getMessage());
    }
    }
        
        // elseif (Auth::guard('employee')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
    
        //     return redirect()->route('employee.dashboard')->with('success', 'You are logged in as an employee');
        // }
        
        // return redirect()->back()->withInput($request->only('email', 'remember'))->with('error', 'Please provide correct credentials');
    
    


}
