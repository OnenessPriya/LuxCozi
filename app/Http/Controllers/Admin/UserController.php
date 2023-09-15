<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserArea;
use App\Models\UserAttendance;
use App\Models\State;
use App\Models\Store;
use App\Models\Area;
use App\Models\Team;
use App\Models\Activity;
use App\Models\Visit;
use App\Models\Notification;
use App\Models\DistributorRange;
use App\Models\Collection;
use DB;
use Illuminate\Support\Facades\Hash;
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
           
            ;
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
        $users = User::all();
        $stateDetails=State::where('status',1)->orderby('name')->groupby('name')->get();
        return view('admin.user.create', compact('users','stateDetails'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         //dd($request->all());
         $request->validate([
            "name" => "required",
            "fname" => "required|string|unique:users|max:255",
            "lname" => "required|string|unique:users|max:255",
            "email" => "nullable|string|max:255|unique:users,email",
            "mobile" => "required|integer|digits:10",
            "whatsapp_no" => "nullable|integer|digits:10",
            "dob" => "nullable",
            "gender" => "nullable|string",
            "user_type" => "nullable",
            "employee_id" => "required|string|min:1",
            "address" => "nullable|string",
            "landmark" => "nullable|string",
            "state" => "required|string",
            "city" => "required|string",
            "aadhar_no" => "nullable|string",
            "pan_no" => "nullable|string",
            "pin" => "nullable|integer|digits:6",
            "password" => "required",
            "image"    =>"nullable|mimes:jpg,jpeg,png,svg,gif|max:10000000"
        ]);

        $collectedData = $request->except('_token');
        $newEntry = new User;
        $newEntry->fname = $collectedData['fname'];
        $newEntry->lname = $collectedData['lname'];
		$newEntry->name = $collectedData['name'];
        $newEntry->email = $collectedData['email'];
        $newEntry->mobile = $collectedData['mobile'];
        $newEntry->whatsapp_no = $collectedData['whatsapp_no'];
        $newEntry->dob = $collectedData['dob'];
        $newEntry->gender = $collectedData['gender'];
        $newEntry->employee_id = $collectedData['employee_id'];
        $newEntry->type = $collectedData['type'];
        $newEntry->address = $collectedData['address'];
        $newEntry->landmark = $collectedData['landmark'];
        $newEntry->state = $collectedData['state'];
        $newEntry->city = $collectedData['city'];
        $newEntry->pin = $collectedData['pin'];
        $newEntry->aadhar_no = $collectedData['aadhar_no'];
        $newEntry->pan_no = $collectedData['pan_no'];
        $newEntry->password = Hash::make($collectedData['password']);
        if($newEntry->image){
        $upload_path = "uploads/user/";
        $image = $collectedData['image'];
        $imageName = time() . "." . $image->getClientOriginalName();
        $image->move($upload_path, $imageName);
        $uploadedImage = $imageName;
        $newEntry->image = $upload_path . $uploadedImage;
		}
        $newEntry->save();

        if ($newEntry) {
            return redirect()->route('admin.users.index');
        } else {
            return redirect()->route('admin.users.index')->withInput($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $data = (object) [];
        $data->user = User::findOrFail($id);

        // VP
        if ($data->user->type == 1) {
            $user_type = $request->user_type ? $request->user_type : '';
            $name = $request->name ? $request->name : '';
            $state = $request->state ? $request->state : '';
            $area = $request->area ? $request->area : '';

            $query = Team::where('nsm_id', $data->user->id);

            $query->when($user_type, function($query) use ($user_type, $name) {
                if(!empty($name)) {
                    if ($user_type == 1) $user_type = 'vp';
                    elseif ($user_type == 2) $user_type = 'rsm';
                    elseif ($user_type == 3) $user_type = 'asm';
                    elseif ($user_type == 4) $user_type = 'ase';
                    elseif ($user_type == 5) $user_type = 'distributor_name';
                    else $user_type = 'retailer';

                    $query->where($user_type, $name);
                } else {
                    if ($user_type == 2) {
                        $user_type = 'rsm';
                        $query->where('rsm', '!=', null)->groupBy('rsm');
                    } elseif ($user_type == 3) {
                        $user_type = 'asm';
                        $query->where('asm', '!=', null)->groupBy('asm');
                    } elseif ($user_type == 4) {
                        $user_type = 'ase';
                        $query->where('ase', '!=', null)->groupBy('ase');
                    } elseif ($user_type == 5) {
                        $user_type = 'distributor_name';
                        $query->where('distributor_name', '!=', null)->groupBy('distributor_name');
                    } else {
                        $user_type = 'retailer';
                        $query->where('retailer', '!=', null)->groupBy('retailer');
                    }
                }
            });

            $query->when($state, function($query) use ($state) {
                $query->where('state', $state);
            });
            $query->when($area, function($query) use ($area) {
                $query->where('area', $area);
            });

            $data->team = $query->paginate(25);
            
            return view('admin.user.detail.nsm', compact('data', 'id', 'request'));
        }
        //ZSM
         elseif ($data->user->type == 2) {
            $user_type = $request->user_type ? $request->user_type : '';
            $name = $request->name ? $request->name : '';
            $state = $request->state ? $request->state : '';
            $area = $request->area ? $request->area : '';

            $query = Team::where('zsm_id', $data->user->id);

            $query->when($user_type, function($query) use ($user_type, $name) {
                if(!empty($name)) {
                    if ($user_type == 1) $user_type = 'vp';
                    elseif ($user_type == 2) $user_type = 'rsm';
                    elseif ($user_type == 3) $user_type = 'asm';
                    elseif ($user_type == 4) $user_type = 'ase';
                    elseif ($user_type == 5) $user_type = 'distributor_name';
                    else $user_type = 'retailer';

                    $query->where($user_type, $name);
                } else {
                    if ($user_type == 2) {
                        $user_type = 'rsm';
                        $query->where('rsm', '!=', null)->groupBy('rsm');
                    } elseif ($user_type == 3) {
                        $user_type = 'asm';
                        $query->where('asm', '!=', null)->groupBy('asm');
                    } elseif ($user_type == 4) {
                        $user_type = 'ase';
                        $query->where('ase', '!=', null)->groupBy('ase');
                    } elseif ($user_type == 5) {
                        $user_type = 'distributor_name';
                        $query->where('distributor_name', '!=', null)->groupBy('distributor_name');
                    } else {
                        $user_type = 'retailer';
                        $query->where('retailer', '!=', null)->groupBy('retailer');
                    }
                }
            });

            $query->when($state, function($query) use ($state) {
                $query->where('state', $state);
            });
            $query->when($area, function($query) use ($area) {
                $query->where('area', $area);
            });

            $data->team = $query->paginate(25);
            
            return view('admin.user.detail.rsm', compact('data', 'id', 'request'));
        }
         // SM
         elseif ($data->user->type == 3) {
            $user_type = $request->user_type ? $request->user_type : '';
            $name = $request->name ? $request->name : '';
            $state = $request->state ? $request->state : '';
            $area = $request->area ? $request->area : '';

            $query = Team::where('rsm_id', $data->user->id);

            $query->when($user_type, function($query) use ($user_type, $name) {
                if(!empty($name)) {
                    if ($user_type == 1) $user_type = 'vp';
                    elseif ($user_type == 2) $user_type = 'rsm';
                    elseif ($user_type == 3) $user_type = 'asm';
                    elseif ($user_type == 4) $user_type = 'ase';
                    elseif ($user_type == 5) $user_type = 'distributor_name';
                    else $user_type = 'retailer';

                    $query->where($user_type, $name);
                } else {
                    if ($user_type == 2) {
                        $user_type = 'rsm';
                        $query->where('rsm', '!=', null)->groupBy('rsm');
                    } elseif ($user_type == 3) {
                        $user_type = 'asm';
                        $query->where('asm', '!=', null)->groupBy('asm');
                    } elseif ($user_type == 4) {
                        $user_type = 'ase';
                        $query->where('ase', '!=', null)->groupBy('ase');
                    } elseif ($user_type == 5) {
                        $user_type = 'distributor_name';
                        $query->where('distributor_name', '!=', null)->groupBy('distributor_name');
                    } else {
                        $user_type = 'retailer';
                        $query->where('retailer', '!=', null)->groupBy('retailer');
                    }
                }
            });

            $query->when($state, function($query) use ($state) {
                $query->where('state', $state);
            });
            $query->when($area, function($query) use ($area) {
                $query->where('area', $area);
            });

            $data->team = $query->paginate(25);
            
            return view('admin.user.detail.rsm', compact('data', 'id', 'request'));
        }
        // RSM
        elseif ($data->user->type == 4) {
            $user_type = $request->user_type ? $request->user_type : '';
            $name = $request->name ? $request->name : '';
            $state = $request->state ? $request->state : '';
            $area = $request->area ? $request->area : '';

            $query = Team::where('asm_id', $data->user->id);

            $query->when($user_type, function($query) use ($user_type, $name) {
                if(!empty($name)) {
                    if ($user_type == 1) $user_type = 'vp';
                    elseif ($user_type == 2) $user_type = 'rsm';
                    elseif ($user_type == 3) $user_type = 'asm';
                    elseif ($user_type == 4) $user_type = 'ase';
                    elseif ($user_type == 5) $user_type = 'distributor_name';
                    else $user_type = 'retailer';

                    $query->where($user_type, $name);
                } else {
                    if ($user_type == 2) {
                        $user_type = 'rsm';
                        $query->where('rsm', '!=', null)->groupBy('rsm');
                    } elseif ($user_type == 3) {
                        $user_type = 'asm';
                        $query->where('asm', '!=', null)->groupBy('asm');
                    } elseif ($user_type == 4) {
                        $user_type = 'ase';
                        $query->where('ase', '!=', null)->groupBy('ase');
                    } elseif ($user_type == 5) {
                        $user_type = 'distributor_name';
                        $query->where('distributor_name', '!=', null)->groupBy('distributor_name');
                    } else {
                        $user_type = 'retailer';
                        $query->where('retailer', '!=', null)->groupBy('retailer');
                    }
                }
            });

            $query->when($state, function($query) use ($state) {
                $query->where('state', $state);
            });
            $query->when($area, function($query) use ($area) {
                $query->where('area', $area);
            });

            $data->team = $query->paginate(25);
            
            return view('admin.user.detail.rsm', compact('data', 'id', 'request'));
        }
        // ASM
        elseif ($data->user->type == 5) {
            $user_type = $request->user_type ? $request->user_type : '';
            $name = $request->name ? $request->name : '';
            $state = $request->state ? $request->state : '';
            $area = $request->area ? $request->area : '';

            $query = Team::where('asm_id', $data->user->id);

            $query->when($user_type, function($query) use ($user_type, $name) {
                if(!empty($name)) {
                    if ($user_type == 1) $user_type = 'vp';
                    elseif ($user_type == 2) $user_type = 'rsm';
                    elseif ($user_type == 3) $user_type = 'asm';
                    elseif ($user_type == 4) $user_type = 'ase';
                    elseif ($user_type == 5) $user_type = 'distributor_name';
                    else $user_type = 'retailer';

                    $query->where($user_type, $name);
                } else {
                    if ($user_type == 2) {
                        $user_type = 'rsm';
                        $query->where('rsm', '!=', null)->groupBy('rsm');
                    } elseif ($user_type == 3) {
                        $user_type = 'asm';
                        $query->where('asm', '!=', null)->groupBy('asm');
                    } elseif ($user_type == 4) {
                        $user_type = 'ase';
                        $query->where('ase_id', '!=', null)->groupBy('ase_id');
                    } elseif ($user_type == 5) {
                        $user_type = 'distributor_id';
                        $query->where('distributor_id', '!=', null)->groupBy('distributor_id');
                    } else {
                        $user_type = 'retailer';
                        $query->where('store_id', '!=', null)->groupBy('store_id');
                    }
                }
            });

            $query->when($state, function($query) use ($state) {
                $query->where('state_id', $state);
            });
            $query->when($area, function($query) use ($area) {
                $query->where('area_id', $area);
            });

            $data->team = $query->paginate(25);
            
            return view('admin.user.detail.asm', compact('data', 'id', 'request'));
        }
        // ASE
        elseif ($data->user->type == 6) {
            $data->team = Team::where('ase_id', $data->user->id)->where('store_id', null)->first();
            $data->workAreaList = UserArea::where('user_id', $data->user->id)->get();
            $data->distributorList = Team::where('ase_id', $data->user->id)->where('distributor_id', '!=', null)->groupBy('distributor_id')->orderBy('id','desc')->with('distributors')->get();
            $data->storeList = Store::where('user_id',$data->user->id)->orderBy('name')->get();
            $data->areaDetail=Area::where('status',1)->orderby('name')->get();
            return view('admin.user.detail.ase', compact('data', 'id', 'request'));
        }
        // Distributor
        elseif ($data->user->type == 7) {
            $area=Area::where('name', $data->user->city)->first();
            $data->team = Team::where('distributor_id', $data->user->id)->where('area_id', $area->id)->first();
            $data->distributor = User::where('name', $data->user->name)->first();
			$data->storeList = Team::where('distributor_id', $data->user->id)->where('store_id','!=',null)->groupBy('store_id')->with('store')->get();
            return view('admin.user.detail.distributor', compact('data', 'id', 'request'));
        }
        // Retailer
        else {
            $user = User::findOrFail($id);
            $data = Store::where('name', $user->name)->with('user')->first();

            return view('admin.store.detail', compact('data', 'id', 'request'));
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data=User::findOrfail($id);
        $data->stateDetails = State::where('status',1)->orderby('name')->groupby('name')->get();
        $data->allNSM = User::select('name')->where('type', '=', 1)->groupBy('name')->orderBy('name')->get();
        $data->allZSM = User::select('name')->where('type', '=', 2)->groupBy('name')->orderBy('name')->get();
        $data->allRSM = User::select('name')->where('type', '=', 3)->groupBy('name')->orderBy('name')->get();
        $data->allSM = User::select('name')->where('type', '=', 4)->groupBy('name')->orderBy('name')->get();
        $data->allASM = User::select('name')->where('type', '=', 5)->groupBy('name')->orderBy('name')->get();
        $data->allASE = User::select('name')->where('type', '=', 6)->groupBy('name')->orderBy('name')->get();
        $data->allDistributor = User::select('name')->where('type', '=', 7)->groupBy('name')->orderBy('name')->get();
        
        return view('admin.user.edit', compact('data'));
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
        $request->validate([
            "type" => "required|integer|in:1,2,3,4,5,6",
            "designation" => "nullable|string|max:255",
            "name" => "required|string|max:255",
            "fname" => "required|string|max:255",
            "lname" => "required|string|max:255",
            "employee_id" => "required|string|max:255",
            "mobile" => "required|integer|digits:10",
            "email" => "nullable|string|max:255",
            "state" => "required|string|max:255",
            "area" => "nullable|string|max:255"
          
        ]);

        $updateEntry = User::findOrFail($id);
        $updateEntry->type = $request->type;
        $updateEntry->designation = $request->designation;
        $updateEntry->name = $request->name;
        $updateEntry->fname = $request->fname;
        $updateEntry->lname = $request->lname;
        $updateEntry->mobile = $request->mobile;
        $updateEntry->employee_id = $request->employee_id;
        $updateEntry->email = $request->email;
        $updateEntry->state = $request->state;
        if(!empty($request->area)){
         $updateEntry->city = $request->area;
        }
        // password
        if(!empty($request->password)) $updateEntry->password = Hash::make($request->password);

        $updateEntry->save();
		// insert into Team
        $teamDetails =  Team::select('nsm_id','zsm_id','rsm_id','sm_id','asm_id','ase_id')->where('distributor_id',$id)->groupBy('distributor_id')->first();
        if ($updateEntry) {
            return redirect()->route('admin.users.edit', $id)->with('success', 'User detail updated successfully');
        } else {
            return redirect()->route('admin.users.edit', $id)->with('failure', 'Something happened')->withInput($request->all());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data=User::destroy($id);
        if ($data) {
            return redirect()->route('admin.users.index');
        } else {
            return redirect()->route('admin.users.index')->withInput($request->all());
        }
    }

     /**
     * status change the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id)
    {
        $category = User::findOrFail($id);
        $status = ( $category->status == 1 ) ? 0 : 1;
        $category->status = $status;
        $category->save();
        if ($category) {
            return redirect()->route('admin.users.index');
        } else {
            return redirect()->route('admin.users.create')->withInput($request->all());
        }
    }
    //password generate 
    public function passwordGenerate(Request $request)
    {
        $userDetail = User::findOrFail($request->userId);
        $explodedName = explode(' ', $userDetail->name);
        $var1 = ucwords(strtolower($explodedName[0]));

        $state = $userDetail->state;
            $var2 = strtoupper($userDetail->employee_id);

            if (!empty($var2)) {
                $newGeneratedPassword = $var1.$var2;

                return response()->json([
                    'status' => 200,
                    'message' => 'Password generated',
                    'data' => $newGeneratedPassword
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid Employee code. Please generate manually'
                ]);
            }

       
    }
    //reset password
    public function passwordReset(Request $request)
    {
        $updateEntry = User::findOrFail($request->id);
        if(!empty($request->password)) $updateEntry->password = Hash::make($request->password);
        $updateEntry->save();

        if ($updateEntry) {
            return redirect()->back()->with('success', 'Password changed successfully');
        } else {
            return redirect()->back()->with('failure', 'Something happened');
        }
    }
    //distributor collection tagging
    public function collection(Request $request, $id)
    {
		$data = DistributorRange::where('distributor_id', $id)->with('users')->get();
		$collections = Collection::where('status', 1)->orderBy('position')->get();
        $distributor = User::findOrFail($id);
        $aseList = Team::where('distributor_id',$distributor->id)->orderBy('ase_id')->groupby('ase_id')->with('ase','states','areas')->get();
		
        return view('admin.user.distributor-range', compact('data', 'collections', 'id', 'distributor', 'aseList'));
    }

	public function collectionCreate(Request $request, $id)
    {
		$request->validate([
			"collection_id" => "required|integer|min:1",
			"user_id" => "required|integer|min:1",
            "distributor_id" => "required|integer|min:1",
		]);

		$check = DistributorRange::where('distributor_id', $request->distributor_id)->where('collection_id', $request->collection_id)->first();

		if($check) {
			return redirect()->back()->with('failure', 'This Range already exists to this Distributor');
		} else {
			DB::table('distributor_ranges')->insert([
                'collection_id' => $request->collection_id,
                'user_id' => $request->user_id,
                'distributor_id' => $request->distributor_id
            ]);
		}

		return redirect()->back()->with('success', 'Range Added to this Distributor');
    }

	public function collectionDelete(Request $request, $id)
    {
		$data = DistributorRange::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Range Deleted for this Distributor');
    }
    //areacreate for ASE
    public function areaStore(Request $request)
    {
		$request->validate([
			"area_id" => "required|integer|min:1",
			"user_id" => "required|integer|min:1",
		]);

		$check = UserArea::where('user_id', $request->user_id)->where('area_id', $request->area_id)->first();

		if($check) {
			return redirect()->back()->with('failure', 'This Area already exists to this ASE');
		} else {
			DB::table('user_areas')->insert([
                'area_id' => $request->area_id,
                'user_id' => $request->user_id
            ]);
		}

		return redirect()->back()->with('success', 'Area Added successfully');
    }
    //activity list
    public function activityList(Request $request)
    {
        if (isset($request->date_from) || isset($request->date_to) || isset($request->user_id)) {
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $user_id = $request->user_id ? $request->user_id : '';

            $query = Activity::query();

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('date', '<=', $date_to);
            });
           
            $query->when($user_id, function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });

            $data = $query->latest('id')->paginate(25);
        } else {
            $data = Activity::latest('id')->paginate(25);
        }
        $user = User::select('id','name')->where('type',6)->orWhere('type',5)->where('name', '!=', null)->orderBy('name')->get();
        return view('admin.activity.index', compact('data', 'request','user'));
    
    }

    //activity csv export
    public function activityCSV(Request $request)
    {
        if (isset($request->date_from) || isset($request->date_to) || isset($request->user_id)) {
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $user_id = $request->user_id ? $request->user_id : '';

            $query = Activity::query();

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('date', '<=', $date_to);
            });
           
            $query->when($user_id, function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });

            $data = $query->latest('id')->with('users')->get();
        } else {
            $data = Activity::latest('id')->with('users')->get();
        }


        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-user-activity-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'USER', 'TYPE', 'COMMENT', 'LOCATION', 'DATETIME');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                $datetime = date('j F, Y h:i A', strtotime($row['created_at']));

                $lineData = array(
                    $count,
                    $row->users->name ?? '',
                    $row['users']['type'],
                    $row['comment'],
                    $row['location'],
                    $datetime
                );

                fputcsv($f, $lineData, $delimiter);

                $count++;
            }

            // Move back to beginning of file
            fseek($f, 0);

            // Set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');

            //output all remaining data on a file pointer
            fpassthru($f);
        }
    }

    //notification list
    public function notificationList(Request $request)
    {
        $date_from = $request->from ? $request->from : '';
        $date_to = $request->to ? $request->to : '';
        $keyword = $request->keyword ? $request->keyword : '';

        $query = Notification::query();

        $query->when($date_from, function($query) use ($date_from) {
            $query->where('created_at', '>=', $date_from);
        });
        $query->when($date_to, function($query) use ($date_to) {
            $query->where('created_at', '<=', date('Y-m-d', strtotime($date_to.'+1 day')));
        });
        $query->when($keyword, function($query) use ($keyword) {
            $query->where('title', 'like', '%'.$keyword.'%')
            ->orWhere('body', 'like', '%'.$keyword.'%');
        });

        $data = $query->where('receiver_id','admin')->latest('id')->with('senderDetails')->paginate(25);

        return view('admin.notification.index', compact('data','request'));

    }

     //state wise area
     public function state(Request $request, $state)
     {
         $stateName=State::where('name',$state)->first();
         $region = Area::where('state_id',$stateName->id)->get();
         $resp = [
             'state' => $stateName->name,
             'area' => [],
         ];
 
         foreach($region as $area) {
             $resp['area'][] = [
                 'area_id' => $area->id,
                 'area' => $area->name,
             ];
         }
       
         return response()->json(['error' => false, 'resp' => 'State wise area list', 'data' => $resp]);
     }


      //attendance list
    public function attendanceList(Request $request)
    {
        if (isset($request->date_from) || isset($request->date_to) || isset($request->keyword)) {
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $keyword = $request->keyword ? $request->keyword : '';

            $query = UserAttendance::join('users', 'user_attendances.user_id', 'users.id');

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('user_attendances.entry_date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('user_attendances.entry_date', '<=', $date_to);
            });
           
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('users.name', 'like', '%'.$keyword.'%');
            });

            $data = $query->orderby('entry_date','desc')->groupby('entry_date','user_id')->paginate(25);
        } else {
            $data = UserAttendance::orderby('entry_date','desc')->groupby('entry_date','user_id')->paginate(25);
        }
        
        return view('admin.attendance.index', compact('data', 'request'));
    
    }

    //attendance csv export
    public function attendanceCSV(Request $request)
    {
        if (isset($request->date_from) || isset($request->date_to) || isset($request->keyword)) {
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $keyword = $request->keyword ? $request->keyword : '';

            $query = UserAttendance::join('users', 'user_attendances.user_id', 'users.id');;

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('user_attendances.entry_date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('user_attendances.entry_date', '<=', $date_to);
            });
           
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('users.name', 'like', '%'.$keyword.'%');
            });

            $data = $query->orderby('entry_date','desc')->groupby('entry_date','user_id')->paginate(25);
        } else {
            $data = UserAttendance::orderby('entry_date','desc')->groupby('entry_date','user_id')->paginate(25);
        }


        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-user-attendance-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'SALES PERSON','SALES PERSON EMP ID', 'TYPE', 'TIME-IN', 'TIME-OUT', 'TOTAL HOURS');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                $datetime = date('j F, Y h:i A', strtotime($row['created_at']));
                $hours=\Carbon\Carbon::parse($row->start_time)->diffInHours($row->end_time);
                if($row['users']['type']==6){
                    $type='ASE';
                }
                $lineData = array(
                    $count,
                    $row->users->name ?? '',
                    $row->users->employee_id ?? '',
                    $type,
                    $row->start_time,
                    $row['end_time'],
                    $hours
                );

                fputcsv($f, $lineData, $delimiter);

                $count++;
            }

            // Move back to beginning of file
            fseek($f, 0);

            // Set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');

            //output all remaining data on a file pointer
            fpassthru($f);
        }
    }
}
