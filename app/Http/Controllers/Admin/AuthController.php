<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\User;
use App\Models\Store; 
use AuthenticatesUsers;
class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.auth.login');
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
        
        $request->validate([
            'email' => 'required | string | email | exists:admins',
            'password' => 'required | string'
        ]);
       
        $adminCreds = $request->only('email', 'password');
        
        if ( Auth::guard('admin')->attempt($adminCreds) ) {
          
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('admin.login')->withInputs($request->all())->with('failure', 'Invalid credentials. Try again');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        
        $data = (object)[];
        $data->zsm = User::select('name')->where('type',1)->get();
        $data->nsm = User::select('name')->where('type',2)->get();
        $data->rsm = User::select('name')->where('type',3)->get();
        $data->sm = User::select('name')->where('type',5)->get();
        $data->asm = User::select('name')->where('type',5)->get();
        $data->ase = User::select('name')->where('type',6)->get();
        $data->distributor =User::select('name')->where('type',7)->get();
       // $data->store = Store::where('status','=', 1)->count();
        return view('admin.dashboard.index', compact('data'));
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
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect()->guest(route('admin.login'));
    }
}
