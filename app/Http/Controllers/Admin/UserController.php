<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\State;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_type = $request->user_type ? $request->user_type : '';
        $state = $request->state ? $request->state : '';
        $area = $request->area ? $request->area : '';
        $keyword = $request->keyword ? $request->keyword : '';
    
        $query = User::query();
    
        $query->when($user_type, function($query) use ($user_type) {
            $query->where('type', $user_type);
        });
        $query->when($state, function($query) use ($state) {
            $query->where('state', $state);
        });
        $query->when($area, function($query) use ($area) {
            $query->where('city', $area);
        });
        $query->when($keyword, function($query) use ($keyword) {
            $query->where('name', 'like', '%'.$keyword.'%')
            ->orWhere('fname', 'like', '%'.$keyword.'%')
            ->orWhere('lname', 'like', '%'.$keyword.'%')
            ->orWhere('mobile', 'like', '%'.$keyword.'%')
            ->orWhere('employee_id', 'like', '%'.$keyword.'%')
            ->orWhere('email', 'like', '%'.$keyword.'%')
            ->orWhere('alt_number1', 'like', '%'.$keyword.'%')
            ->orWhere('alt_number2', 'like', '%'.$keyword.'%')
            ->orWhere('alt_number3', 'like', '%'.$keyword.'%')
            ->orWhere('personal_mail', 'like', '%'.$keyword.'%');
        });
    
        $data = $query->latest('id')->paginate(25);
        
		$state = State::where('status',1)->groupBy('name')->orderBy('name')->get();
        return view('admin.user.index', compact('data', 'state', 'request'));
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
