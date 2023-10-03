<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAttendance;
use App\Models\State;
use App\Models\Store;
use App\Models\Area;
use App\Models\UserArea;
use App\Models\Team;
use App\Models\Activity;
use App\Models\Visit;
use App\Models\Notification;
use App\Models\DistributorRange;
use App\Models\Collection;
use App\Models\HeadQuater;
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
		$hq=HeadQuater::where('status',1)->orderby('name')->groupby('name')->get();
        return view('admin.user.create', compact('users','stateDetails','hq'));
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
            "fname" => "required|string|max:255",
            "lname" => "required|string|max:255",
            "email" => "nullable|string|max:255",
            "mobile" => "required|integer|digits:10",
            "whatsapp_no" => "nullable|integer|digits:10",
            "type" => "required",
			 "designation" =>"required",
            "employee_id" => "required|string|min:1",
            "address" => "nullable|string",
            "landmark" => "nullable|string",
            "state" => "required|string",
            "area" => "nullable|string",
            "headquater" => "nullable|string",
            "password" => "required"
            
        ]);
		
        $collectedData = $request->except('_token');
        $newEntry = new User;
        $newEntry->fname = $collectedData['fname'];
        $newEntry->lname = $collectedData['lname'];
		$newEntry->name = $collectedData['name'];
        $newEntry->email = $collectedData['email'];
        $newEntry->mobile = $collectedData['mobile'];
        $newEntry->whatsapp_no = $collectedData['whatsapp_no'];
        $newEntry->employee_id = $collectedData['employee_id'];
        $newEntry->type = $collectedData['type'];
        $newEntry->state = $collectedData['state'];
        $newEntry->city = $collectedData['area'];
        $newEntry->headquater = $collectedData['headquater'] ?? '';
        $newEntry->password = Hash::make($collectedData['password']);
        if(!empty($collectedData->image)){
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
            
            return view('admin.user.detail.zsm', compact('data', 'id', 'request'));
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
        // SM
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
            
            return view('admin.user.detail.sm', compact('data', 'id', 'request'));
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
            $data->retailerListOfOcc = Team::where('ase_id', $data->user->id)->where('store_id', null)->first();
            $data->workAreaList = UserArea::where('user_id', $data->user->id)->groupby('area_id')->get();
            $data->distributorList = Team::where('ase_id', $data->user->id)->where('distributor_id', '!=', null)->groupBy('distributor_id')->orderBy('id','desc')->get();
			 $data->areaDetail= Area::orderby('name')->get();
            $data->storeList = Store::where('user_id',$data->user->id)->orderBy('name')->get();
			$data->team = Team::where('ase_id', $data->user->id)->first();
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
        $data->allNSM = User::select('name','id')->where('type', '=', 1)->groupBy('name')->orderBy('name')->get();
        $data->allZSM = User::select('name','id')->where('type', '=', 2)->groupBy('name')->orderBy('name')->get();
        $data->allRSM = User::select('name','id')->where('type', '=', 3)->groupBy('name')->orderBy('name')->get();
        $data->allSM = User::select('name','id')->where('type', '=', 4)->groupBy('name')->orderBy('name')->get();
        $data->allASM = User::select('name','id')->where('type', '=', 5)->groupBy('name')->orderBy('name')->get();
        $data->allASE = User::select('name','id')->where('type', '=', 6)->groupBy('name')->orderBy('name')->get();
        $data->allDistributor = User::select('name','id')->where('type', '=', 7)->groupBy('name')->orderBy('name')->get();
        $hq=HeadQuater::where('status',1)->orderby('name')->groupby('name')->get();
        return view('admin.user.edit', compact('data','hq'));
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
       //dd($request->all());
        $request->validate([
            "type" => "required|integer",
            "designation" => "nullable|string|max:255",
            "name" => "required|string|max:255",
            "fname" => "required|string|max:255",
            "lname" => "required|string|max:255",
            "employee_id" => "nullable|string|max:255",
            "mobile" => "nullable|integer|digits:10",
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
        $updateEntry->whatsapp_no = $request->whatsapp_no;
        $updateEntry->employee_id = $request->employee_id;
        $updateEntry->email = $request->email;
        $updateEntry->state = $request->state;
        if(!empty($request->area)){
         $updateEntry->city = $request->area;
        }
        $updateEntry->headquater = $request['headquater'];
        // password
        if(!empty($request->password)) $updateEntry->password = Hash::make($request->password);

        $updateEntry->save();
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
        $explodedName = $userDetail->fname;
        $var1 = ucwords($explodedName);

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
        if (isset($request->date_from) || isset($request->date_to) || isset($request->ase) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm)) {
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $user_id = $request->ase ? $request->ase : '';
            $asm=$request->asm ? $request->asm : '';
            $rsm=$request->rsm ? $request->rsm : '';
            $zsm=$request->zsm ? $request->zsm : '';
            $query = Activity::query();

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('date', '<=', $date_to);
            });
           
            if(!empty($request->ase))
            {
                $query->when($user_id, function($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                });
            }elseif(!empty($request->asm)){
                $query->when($asm, function($query) use ($asm) {
                    $query->where('user_id', $asm);
                });
            }elseif(!empty($request->rsm)){
                $query->when($rsm, function($query) use ($rsm) {
                    $query->where('user_id', $rsm);
                });
            }else{
                $query->when($zsm, function($query) use ($zsm) {
                    $query->where('user_id', $zsm);
                });
            }

            $data = $query->latest('id')->paginate(25);
        } else {
            $data = Activity::latest('id')->paginate(25);
        }
        $user = User::select('id','name')->where('type',6)->orWhere('type',5)->where('name', '!=', null)->orderBy('name')->get();
        $zsm=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();
        return view('admin.activity.index', compact('data', 'request','user','zsm'));
    
    }

       //activity csv export
    public function activityCSV(Request $request)
    {
        if (isset($request->date_from) || isset($request->date_to) || isset($request->ase) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm)) {
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $user_id = $request->ase ? $request->ase : '';
            $asm=$request->asm ? $request->asm : '';
            $rsm=$request->rsm ? $request->rsm : '';
            $zsm=$request->zsm ? $request->zsm : '';
            $query = Activity::query();

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('date', '<=', $date_to);
            });
           
            if(!empty($request->ase))
            {
                $query->when($user_id, function($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                });
            }elseif(!empty($request->asm)){
                $query->when($asm, function($query) use ($asm) {
                    $query->where('user_id', $asm);
                });
            }elseif(!empty($request->rsm)){
                $query->when($rsm, function($query) use ($rsm) {
                    $query->where('user_id', $rsm);
                });
            }else{
                $query->when($zsm, function($query) use ($zsm) {
                    $query->where('user_id', $zsm);
                });
            }

            $data = $query->latest('id')->paginate(25);
        } else {
            $data = Activity::latest('id')->paginate(25);
        }


        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-user-activity-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR','NSM', 'ZSM','RSM','SM','ASM','Employee','Employee Id','Employee Status','Employee Designation','Employee Date of Joining','Employee HQ','Employee Contact No', 'Type', 'Date','Time','Comment', 'Location', 'DateTime');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                $datetime = date('j F, Y h:i A', strtotime($row['created_at']));
                $findTeamDetails= findTeamDetails($row->users->id, $row->users->type);
                $lineData = array(
                    $count,
                    $findTeamDetails[0]['nsm'] ?? '',
                    $findTeamDetails[0]['zsm']?? '',
                    $findTeamDetails[0]['rsm']?? '',
                    $findTeamDetails[0]['sm']?? '',
                    $findTeamDetails[0]['asm']?? '',
                    $row->users ? $row->users->name : '',
                    $row->users->employee_id ?? '',
                    ($row->users->status == 1)  ? 'Active' : 'Inactive',
                    $row->users->designation?? '',
                    $row->users->date_of_joining?? '',
                    $row->users->headquater?? '',
                    $row->users->mobile,
                    $row['type'],
                    $row['date'],
                    $row['time'],
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
        if (isset($request->date_from) || isset($request->date_to) || isset($request->keyword)||isset($request->ase) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm)) {
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $keyword = $request->keyword ? $request->keyword : '';
            $user_id = $request->ase ? $request->ase : '';
            $asm=$request->asm ? $request->asm : '';
            $rsm=$request->rsm ? $request->rsm : '';
            $zsm=$request->zsm ? $request->zsm : '';
            $query = UserAttendance::select('user_attendances.id','user_attendances.user_id','user_attendances.entry_date','user_attendances.type','user_attendances.start_time','user_attendances.end_time','user_attendances.other_activities_id')->join('users', 'user_attendances.user_id', 'users.id');

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('user_attendances.entry_date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('user_attendances.entry_date', '<=', $date_to);
            });
            
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('users.name', 'like', '%'.$keyword.'%');
            });

            if(!empty($request->ase))
            {
                $query->when($user_id, function($query) use ($user_id) {
                    $query->where('user_attendances.user_id', $user_id);
                });
            }elseif(!empty($request->asm)){
                $query->when($asm, function($query) use ($asm) {
                    $query->where('user_attendances.user_id', $asm);
                });
            }elseif(!empty($request->rsm)){
                $query->when($rsm, function($query) use ($rsm) {
                    $query->where('user_attendances.user_id', $rsm);
                });
            }else{
                $query->when($zsm, function($query) use ($zsm) {
                    $query->where('user_attendances.user_id', $zsm);
                });
            }

            $data = $query->whereNotIn('users.type', [1,4,7])->orderby('user_attendances.entry_date','desc')->groupby('user_attendances.entry_date','user_attendances.user_id')->paginate(25);
            //dd($data);
        } else {
            $data = UserAttendance::orderby('entry_date','desc')->groupby('entry_date','user_id')->paginate(25);
        }
        $zsmDetails=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();

        return view('admin.attendance.index', compact('data', 'request','zsmDetails'));
    
    }

    
    //attendance csv export
    public function attendanceListCSV(Request $request)
    {
        if (isset($request->date_from) || isset($request->date_to) || isset($request->keyword)||isset($request->ase) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm)) {
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $keyword = $request->keyword ? $request->keyword : '';
            $user_id = $request->ase ? $request->ase : '';
            $asm=$request->asm ? $request->asm : '';
            $rsm=$request->rsm ? $request->rsm : '';
            $zsm=$request->zsm ? $request->zsm : '';
            $query = UserAttendance::select('user_attendances.id','user_attendances.user_id','user_attendances.entry_date','user_attendances.type','user_attendances.start_time','user_attendances.end_time','user_attendances.other_activities_id')->join('users', 'user_attendances.user_id', 'users.id');

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('user_attendances.entry_date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('user_attendances.entry_date', '<=', $date_to);
            });
            
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('users.name', 'like', '%'.$keyword.'%');
            });

            if(!empty($request->ase))
            {
                $query->when($user_id, function($query) use ($user_id) {
                    $query->where('user_attendances.user_id', $user_id);
                });
            }elseif(!empty($request->asm)){
                $query->when($asm, function($query) use ($asm) {
                    $query->where('user_attendances.user_id', $asm);
                });
            }elseif(!empty($request->rsm)){
                $query->when($rsm, function($query) use ($rsm) {
                    $query->where('user_attendances.user_id', $rsm);
                });
            }else{
                $query->when($zsm, function($query) use ($zsm) {
                    $query->where('user_attendances.user_id', $zsm);
                });
            }

            $data = $query->whereNotIn('users.type', [1,4,7])->orderby('user_attendances.entry_date','desc')->groupby('user_attendances.entry_date','user_attendances.user_id')->paginate(25);
            //dd($data);
        } else {
            $data = UserAttendance::orderby('entry_date','desc')->groupby('entry_date','user_id')->paginate(25);
        }


        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-user-attendance-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'NSM', 'ZSM','RSM','SM','ASM','Employee','Employee Id','Employee Status','Employee Designation','Employee Date of Joining','Employee HQ','Employee Contact No', 'TYPE','NOTE','TIME-IN', 'TIME-OUT', 'TOTAL HOURS');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                $findTeamDetails= findTeamDetails($row->users->id, $row->users->type);
                $hours=\Carbon\Carbon::parse($row->start_time)->diffInHours($row->end_time);
                if($row['users']['type']==6){
                    $type='ASE';
                }
                if($row->type=='leave'){
                     $leave ='leave';
                }elseif($row->type=='distributor-visit'){
                     $leave ='Distributor Visit'; 
                   
                }elseif($row->type=='meeting'){
                    $leave = 'Meeting';
                    
                    
                }else{
                    $leave ='Present';
                }
                if($row->type!='leave'){
                    $startTime=$row->start_time;
                    $endTime=$row->end_time ;
                } 
                $lineData = array(
                    $count,
                    $findTeamDetails[0]['nsm'] ?? '',
                    $findTeamDetails[0]['zsm']?? '',
                    $findTeamDetails[0]['rsm']?? '',
                    $findTeamDetails[0]['sm']?? '',
                    $findTeamDetails[0]['asm']?? '',
                    $row->users ? $row->users->name : '',
                    $row->users->employee_id ?? '',
                    ($row->users->status == 1)  ? 'Active' : 'Inactive',
                    $row->users->designation?? '',
                    $row->users->date_of_joining?? '',
                    $row->users->headquater?? '',
                    $row->users->mobile,
                    
                    $leave,
                    $row->otheractivity->reason ?? '',
                    $startTime,
                    $endTime,
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
	
	
	//zsm wise rsm list
    public function zsmwiseRsm(Request $request,$id)
    {
       $data=Team::where('zsm_id',$id)->with('rsm:id,name')->groupby('rsm_id')->get();

       if (count($data)==0) {
                return response()->json(['error'=>true, 'resp'=>'No data found']);
       } else {
                return response()->json(['error'=>false, 'resp'=>'Rsm List','data'=>$data]);
       } 
        
    }
    //rsm wise sm list
    public function rsmwiseSm(Request $request,$id)
    {
       $data=Team::where('rsm_id',$id)->with('sm:id,name')->groupby('sm_id')->get();
       
       if (count($data)==0) {
                return response()->json(['error'=>true, 'resp'=>'No data found']);
       } else {
                return response()->json(['error'=>false, 'resp'=>'Sm List','data'=>$data]);
       } 
        
    }
    //sm wise asm list
    public function smwiseAsm(Request $request,$id)
    {
       $data=Team::where('sm_id',$id)->with('asm:id,name')->groupby('asm_id')->get();
       
       if (count($data)==0) {
                return response()->json(['error'=>true, 'resp'=>'No data found']);
       } else {
                return response()->json(['error'=>false, 'resp'=>'Asm List','data'=>$data]);
       } 
        
    }
    //sm wise asm and ase
    public function smwiseAsmAse(Request $request,$id)
    {
       $data=Team::where('sm_id',$id)->with('asm:id,name','ase:id,name')->groupby('asm_id','ase_id')->get();
       
       if (count($data)==0) {
            return response()->json(['error'=>true, 'resp'=>'No data found']);
       } else {
            return response()->json(['error'=>false, 'resp'=>'ASM 7 ASE List','data'=>$data]);
       } 
        
    }
     //asm wise ase list
     public function asmwiseAse(Request $request,$id)
     {
        $data=Team::where('asm_id',$id)->with('ase:id,name')->groupby('ase_id')->get();
        
        if (count($data)==0) {
                 return response()->json(['error'=>true, 'resp'=>'No data found']);
        } else {
                 return response()->json(['error'=>false, 'resp'=>'Ase List','data'=>$data]);
        } 
         
     }

     //attendance report for all
     public function attendanceReport(Request $request)
     {
        $zsmDetails=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();

       // $month = !empty($request->month)?$request->month:date('Y-m');
        if (isset($request->month) || isset($request->zsm)|| isset($request->rsm)|| isset($request->sm)|| isset($request->asm)|| isset($request->ase)) {
            
            $month = !empty($request->month)?$request->month:date('Y-m');
            // $date_from = $request->date_from ? $request->date_from : '';
            // $date_to = $request->date_to ? $request->date_to : '';
            $zsm = $request->zsm ? $request->zsm : '';
            $rsm = $request->rsm ? $request->rsm : '';
            $sm = $request->sm ? $request->sm : '';
            $asm = $request->asm ? $request->asm : '';
            $ase = $request->ase ? $request->ase : '';
            $data = User::whereNotIn('type', [1,4,7])->where('id', $zsm)->orWhere('id', $rsm)->orWhere('id', $asm)->orWhere('id', $ase)->paginate(50);
            
            

        } else {
            
            $data = User::whereNotIn('type', [1,4,7])->paginate(50);
            
        }
        $month = !empty($request->month)?$request->month:date('Y-m');
        return view('admin.attendance.report', compact( 'request','zsmDetails','data','month'));
     }

       //employee productivity report for all
     public function employeeProductivity(Request $request)
     {
        $zsmDetails=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();
        if (isset($request->date_from)||isset($request->date_to) || isset($request->zsm)|| isset($request->rsm)|| isset($request->sm)|| isset($request->asm)|| isset($request->ase)) {
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $zsm = $request->zsm ? $request->zsm : '';
            $rsm = $request->rsm ? $request->rsm : '';
            $sm = $request->sm ? $request->sm : '';
            $asm = $request->asm ? $request->asm : '';
            $ase = $request->ase ? $request->ase : '';
            $data = User::whereNotIn('type', [1,2,3,4,7])->where('id', $asm)->orWhere('id', $ase)->paginate(50);
            
            

        } else {
            
            $data = User::whereNotIn('type', [1,2,3,4,7])->paginate(50);
            
        }
        $date_from = !empty($request->month)?$request->month:date('Y-m-01');
        $date_to = !empty($request->month)?$request->month:date('Y-m-d');
        return view('admin.employee-productivity.index', compact('zsmDetails', 'request','data','date_from','date_to'));
     }

     //employee productivity csv export
    public function employeeProductivityCSV(Request $request)
    {
        if (isset($request->date_from) || isset($request->date_to) || isset($request->zsm)|| isset($request->rsm)|| isset($request->sm)|| isset($request->asm)|| isset($request->ase)) {
            
            $date_to = $request->date_to ? $request->date_to : '';
            $keyword = $request->keyword ? $request->keyword : '';

            $query = UserAttendance::join('users', 'user_attendances.user_id', 'users.id')->join('teams', 'teams.ase_id', 'users.id');

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('user_attendances.entry_date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('user_attendances.entry_date', '<=', $date_to);
            });
           
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('users.name', 'like', '%'.$keyword.'%');
            });

            $data = $query->orderby('entry_date','desc')->groupby('entry_date','user_id')->get();
        } else {
            $data = UserAttendance::orderby('entry_date','desc')->groupby('entry_date','user_id')->get();
        }


        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-employee-productivty-report-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('ZSM', 'RSM','SM', 'ASM', 'EMPLOYEE','EMPLOYEE EMP ID','EMPLOYEE STATUS', 'EMPLOYEE DESIGNATION', 'EMPLOYEE AREA','TOTAL DAYS','ACTUAL RETAILING DAY','TOTAL PRESENT','LEAVE/WEEK-OF/HOLIDAY','TOTAL RETAIL COUNT','TOTAL SALES COUNT','TELEPHONIC ORDER COUNT');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                
                $hours=\Carbon\Carbon::parse($row->start_time)->diffInHours($row->end_time);
                if($row['users']['type']==6){
                    $type='ASE';
                }
                if($row->type=='leave'){
                     $leave ='leave';
                }elseif($row->type=='distributor-visit'){
                     $leave ='Distributor Visit'; 
                   
                }elseif($row->type=='meeting'){
                    $leave = 'Meeting';
                    
                    
                }else{
                    $leave ='Present';
                }
                if($row->type!='leave'){
                    $startTime=$row->start_time;
                    $endTime=$row->end_time ;
                } 
                $lineData = array(
                    $count,
                    $row->users->name ?? '',
                    $row->users->employee_id ?? '',
                    $type,
                    $leave,
                    $row->otheractivity->reason ?? '',
                    $startTime,
                    $endTime,
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
	

//team create
    public function userTeamAdd(Request $request)
    {
        //dd($request->all());
		$request->validate([
			"distributor_id" => "required|integer",
			"ase_id" => "required|integer",
            "stateId" => "required",
            "stateId" => "required",
		]);
        $state_id=State::where('name',$request->stateId)->first();
        $area_id=Area::where('name',$request->areaId)->first();
		$newEntry = new Team;
        $newEntry->state_id = $state_id->id;
        $newEntry->area_id = $area_id->id;
		$newEntry->distributor_id = $request['distributor_id'];
        $newEntry->nsm_id = $request['nsm_id'];
        $newEntry->zsm_id = $request['zsm_id'];
        $newEntry->rsm_id = $request['rsm_id'];
        $newEntry->sm_id = $request['sm_id'];
        $newEntry->asm_id = $request['asm_id'];
        $newEntry->ase_id = $request['ase_id'];
		$newEntry->save();
        if($newEntry){
		    return redirect()->back()->with('success', 'Team Added to this Distributor');
        }
    }

     //team update
     public function userTeamEdit(Request $request,$id)
     {
         //dd($request->all());
         $request->validate([
             "distributor_id" => "required|integer",
             "ase_id" => "required|integer",
             "stateId" => "required",
             "stateId" => "required",
         ]);
         $state_id=State::where('name',$request->stateId)->first();
         $area_id=Area::where('name',$request->areaId)->first();
         $newEntry = Team::findOrfail($id);
         $newEntry->state_id = $state_id->id ?? '';
		 if(!empty($request->areaId)){
        	 $newEntry->area_id = $area_id->id ?? '';
		 }
         $newEntry->distributor_id = $request['distributor_id'] ?? '';
         $newEntry->nsm_id = $request['nsm_id'] ?? '';
         $newEntry->zsm_id = $request['zsm_id'] ?? '';
         $newEntry->rsm_id = $request['rsm_id'] ?? '';
         $newEntry->sm_id = $request['sm_id'] ?? '';
         $newEntry->asm_id = $request['asm_id'] ?? '';
         $newEntry->ase_id = $request['ase_id'] ?? '';
         $newEntry->save();
         if($newEntry){
             return redirect()->back()->with('success', 'Team Updated to this Distributor');
         }
     }

    //team delete
    public function userTeamDestroy(Request $request,$id)
    {
		$data = Team::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Team data Deleted for this Distributor');
    }
}
