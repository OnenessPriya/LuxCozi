<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLogin;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
         $validator = Validator::make($request->all(),[
            'mobile' => 'required|digits:10',
            'password' => 'required'
        ]);

        if(!$validator->fails()){
            $mobile = $request->mobile;
            $password = $request->password;

            $userCheck = User::where('mobile', $mobile)->first();
    
            if ($userCheck) {
                if (Hash::check($password, $userCheck->password)) {
    				$status = $userCheck->status;
    					 if ($status == 0) {
    						return response()->json(['error' => true, 'resp' =>  'Your account is temporary blocked. Contact Admin']);
    					}else{
                         return response()->json(['error' => false, 'resp' => 'Login successful', 'data' => $userCheck]);
    					}
                } else {
                    return response()->json(['error' => true, 'resp' => 'You have entered wrong login credential. Please try with the correct one.']);
                }
            } else {
                return response()->json(['error' => true, 'resp' => 'You have entered wrong login credential. Please try with the correct one.']);
            }
        }else {
             return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkLogin($id)
    {
        $data = (object)[];
		$data->login=UserLogin::where('user_id',$id)->orderby('id','desc')->first();
        $data->user=User::where('id',$id)->first();
        if (empty($data->login)) {
            return response()->json(['error'=>false, 'resp'=>'No data found','is_login'=>0]);
        } else {
            return response()->json(['error'=>false, 'resp'=>'Check Login Or Not','is_login'=>$data->login->is_login,'user'=>$data->user]);
        } 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginflagStore(Request $request)
    {
        $request->validate([
			"user_id" => "required|integer",
			"is_login" => "required|integer"
		]);

        DB::table('user_logins')->insert([
            'user_id' => $request->user_id,
            'is_login' => $request->is_login,
			'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
		 return response()->json(['error'=>false, 'resp'=>'Login flag updated successfully']);
    }

}
