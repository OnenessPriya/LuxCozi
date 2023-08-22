<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
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
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
