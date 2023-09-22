<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\User;
use App\Models\State;
use App\Models\Area;
use App\Models\Team;
use App\Models\UserNoOrderReason;
use App\Models\NoOrderReason;
use DB;
class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $allASEs = User::select('id','name')->where('type',6)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allASMs = User::select('id','name')->where('type',5)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allDistributors = User::select('id','name')->where('type',7)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $state = State::where('status',1)->groupBy('name')->orderBy('name')->get();
        $inactiveStore=Store::where('status',0)->groupby('name')->get();
        if(isset($request->date_from) || isset($request->date_to) || isset($request->distributor_id)||isset($request->ase_id)||isset($request->asm_id)||isset($request->state_id)||isset($request->keyword)||isset($request->area_id)) 
        {
            $from = $request->date_from ? $request->date_from : '';
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

            $distributor = $request->distributor_id ? $request->distributor_id : '';
            $ase = $request->ase_id ? $request->ase_id : '';
            $asm = $request->asm_id ? $request->asm_id : '';
            $stateDetails = $request->state_id ? $request->state_id : '';
            $area = $request->area_id ? $request->area_id : '';
            $keyword = $request->keyword ? $request->keyword : '';

            $query = Store::with('states','areas','users')->join('teams', 'teams.store_id', 'stores.id');
            $query->when($distributor, function($query) use ($distributor) {
                $query->whereRaw("find_in_set('".$distributor."',teams.distributor_id)");
            });
            $query->when($ase, function($query) use ($ase) {
                $query->whereRaw("find_in_set('".$ase."',stores.user_id)");
            });
            $query->when($asm, function($query) use ($asm) {
                $query->whereRaw("find_in_set('".$asm."',stores.user_id)");
            });
            $query->when($stateDetails, function($query) use ($stateDetails) {
                $query->where('stores.state_id', $stateDetails);
            });
            $query->when($area, function($query) use ($area) {
                $query->where('stores.area_id', $area);
            });
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('stores.name','=',$keyword)
                ->orWhere('stores.business_name', $keyword)
                ->orWhere('stores.owner_fname', $keyword)
                ->orWhere('stores.contact','=', $keyword);
            })->whereBetween('stores.created_at', [$from, $to]);

            $data = $query->where('stores.user_id','!=','')->latest('stores.id')->paginate(25);
           //  dd($data);
        }
        else{
            $data = Store::selectRaw('stores.*')->join('teams', 'teams.store_id', 'stores.id')->where('stores.user_id','!=','')
            ->with('states','areas','users')->latest('id')->paginate(25);
            //dd($data);
        }
        
        return view('admin.store.index', compact('data','request', 'allASEs', 'allASMs','allDistributors', 'state', 'request','inactiveStore'));
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
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = (object)[];
        $data->stores = Store::where('id',$id)->with('users','states','areas')->first();
        $data->team = Team::where('store_id', $id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();
        $data->users = User::all();
        return view('admin.store.detail', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = (object)[];
        $data->stores = Store::with('users','states','areas')->findOrfail($id);
        $data->states=State::where('status',1)->groupby('name')->orderby('name')->get();
        $data->team = Team::where('store_id', $id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();
        $data->users = User::where('type',6)->orWhere('type',5)->where('name', '!=', NULL)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $data->asms = User::where('type',5)->where('name', '!=', NULL)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $data->allDistributors = User::select('id','name')->where('type',7)->where('name', '!=', NULL)->where('status',1)->groupBy('name')->orderBy('name')->get();
        return view('admin.store.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());
           $request->validate([
            'name' => 'required|string|min:2|max:255',
            'business_name' => 'required|string|min:2|max:255',
            'distributor_id' => 'required',
			'owner_fname' =>'required|string|max:255',
			'owner_lname' =>'required|string|max:255',
            'gst_no' => 'nullable',
            'contact' => 'required|integer|digits:10',
            'whatsapp' => 'nullable|integer|digits:10',
            'email' => 'nullable|email',
			'date_of_birth' =>'nullable',
            'date_of_anniversary' =>'nullable',
            'address' => 'required',
            'area_id' => 'nullable',
            'state_id' => 'nullable',
            'city_id' => 'nullable',
            'pin' => 'required|integer|digits:6',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000000',
        ]);

         
        $store=Store::where('id',$id)->first();
		$new_ase=User::where('id',$request->ase_id)->first();
	
        if($new_ase->type==6){
	  	    $result1 = Team::where('ase_id',$new_ase->id)->where('distributor_id',$request->distributor_id)->orderby('id','ASC')->groupby('distributor_id')->first();
        }else{
            $result1 = Team::where('asm_id',$new_ase->id)->where('distributor_id',$request->distributor_id)->orderby('id','ASC')->groupby('distributor_id')->first();
        
            
        }
        
      
            $nsm = $result1->nsm_id;
            $zsm = $result1->zsm_id;
            $rsm = $result1->rsm_id;
            $sm = $result1->sm_id;
            $asm= $result1->asm_id;
            $ase= $result1->ase_id;
       
     
       $ase_user_detail = User::select('id')->where('id', $new_ase->id)->get();
		
		
        if (empty($ase_user_detail)) {
            return redirect()->back()->with('Please change distributor. No ASE found as user');
        }
		
        // update store table
        $store = Store::findOrFail($id);
        $store->user_id = $request['ase_id'];
        $store->gst_no = $request->gst_no ?? null;

        // slug update
        if ($store->name != $request->name) {
            $slug = Str::slug($request->name, '-');
            $slugExistCount = Store::where('name', $request->name)->count();
            if ($slugExistCount > 0) $slug = $slug.'-'.($slugExistCount);
            $store->slug = $slug;
        }

        $store->name = $request->name ?? null;
        $store->business_name = $request->business_name ?? null;
        $store->store_OCC_number = $request->store_OCC_number ?? null;
		$store->owner_fname = $request->owner_fname ?? null;
		$store->owner_lname = $request->owner_lname ?? null;
        $store->contact = $request->contact ?? null;
        $store->email = $request->email ?? null;
        $store->whatsapp = $request->whatsapp ?? null;
		$store->date_of_birth = $request->date_of_birth ?? null;
		$store->date_of_anniversary = $request->date_of_anniversary ?? null;
        $store->address = $request->address ?? null;
        $store->area_id = $request->area_id;
        $store->state_id = $request->state_id;
        $store->city = $request->area_id;
        $store->pin = $request->pin ?? null;
		$store->contact_person_fname = $request->contact_person_fname ?? null;
		$store->contact_person_lname = $request->contact_person_lname ?? null;
        $store->contact_person_phone = $request->contact_person_phone ?? null;
        $store->contact_person_whatsapp = $request->contact_person_whatsapp ?? null;
        $store->contact_person_date_of_birth = $request->contact_person_date_of_birth ?? null;
        $store->contact_person_date_of_anniversary = $request->contact_person_date_of_anniversary ?? null;

        // image upload
        if($request->hasFile('image')) {
            $imageName = mt_rand().'.'.$request->image->extension();
            $uploadPath = 'public/uploads/store';
            $request->image->move($uploadPath, $imageName);
            $store->image = $uploadPath.'/'.$imageName;
        }
		$store->updated_at = now();
        $store->save();
        // retailer list of occ update
        $team = Team::where('store_id',$store->id)->first();
        $team->nsm_id = $nsm;
        $team->zsm_id = $zsm;
        $team->state_id = $request->state_id;
        $team->distributor_id = $request->distributor_id;
	    $team->ase_id = $ase;
        $team->area_id = $request->area_id;
        $team->store_id = $store->id ?? null;
        $team->rsm_id = $rsm;
        $team->sm_id = $sm;
        $team->asm_id = $asm;
        $team->is_deleted = '0';
		$team->created_at = now();
		$team->updated_at = now();
        $team->save();
        return redirect()->back()->with('success', 'Store information updated successfully');
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store)
    {
        //
    }

     /**
     * status change the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id)
    {
        $category = Store::findOrFail($id);
        $status = ( $category->status == 1 ) ? 0 : 1;
        $category->status = $status;
        $category->save();
        if ($category) {
            return redirect()->route('admin.stores.index');
        } else {
            return redirect()->route('admin.stores.create')->withInput($request->all());
        }
    }

    //state wise area
    public function stateWiseArea(Request $request, $state)
    {
		$stateName=State::where('id',$state)->first();
		$region = Area::where('state_id',$state)->get();
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

    //export data into csv

  public function csvExport(Request $request)
  {
    if(isset($request->date_from) || isset($request->date_to) || isset($request->distributor_id)||isset($request->ase_id)||isset($request->asm_id)||isset($request->state_id)||isset($request->keyword)||isset($request->area_id)) 
    {
        $from = $request->date_from ? $request->date_from : '';
        $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

        $distributor = $request->distributor_id ? $request->distributor_id : '';
        $ase = $request->ase_id ? $request->ase_id : '';
        $asm = $request->asm_id ? $request->asm_id : '';
        $stateDetails = $request->state_id ? $request->state_id : '';
        $area = $request->area_id ? $request->area_id : '';
        $keyword = $request->keyword ? $request->keyword : '';

        $query = Store::selectRaw('stores.*')->with('states','areas','users')->join('teams', 'teams.store_id', 'stores.id');
        $query->when($distributor, function($query) use ($distributor) {
            $query->whereRaw("find_in_set('".$distributor."',teams.distributor_id)");
        });
        $query->when($ase, function($query) use ($ase) {
            $query->whereRaw("find_in_set('".$ase."',stores.user_id)");
        });
        $query->when($asm, function($query) use ($asm) {
            $query->whereRaw("find_in_set('".$asm."',stores.user_id)");
        });
        $query->when($stateDetails, function($query) use ($stateDetails) {
            $query->where('stores.state_id', $stateDetails);
        });
        $query->when($area, function($query) use ($area) {
            $query->where('stores.area_id', $area);
        });
        $query->when($keyword, function($query) use ($keyword) {
            $query->where('stores.name','=',$keyword)
            ->orWhere('stores.business_name', $keyword)
            ->orWhere('stores.owner_fname', $keyword)
            ->orWhere('stores.contact','=', $keyword);
        })->whereBetween('stores.created_at', [$from, $to]);

        $data = $query->where('stores.user_id','!=','')->latest('stores.id')->paginate(25);
         
    }
    else{
        $data = Store::selectRaw('stores.*')->join('teams', 'teams.store_id', 'stores.id')->where('stores.user_id','!=','')
        ->with('states','areas','users')->latest('id')->paginate(25);
        
    }
        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "Lux-store-list-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            
            $fields = array('SR', 'STORE', 'FIRM', 'ADDRESS', 'AREA','PINCODE','STATE','OWNER NAME','MOBILE', 'WHATSAPP', 'CONTACT PERSON', 'CONTACT PERSON PHONE', 'OWNER DATE OF BIRTH', 'OWNER DATE OF ANNIVERSARY','EMAIL', 'GST NUMBER','DISTRIBUTOR', 'CREATED BY', 'ASE','ASM', 'SM','RSM', 'ZSM', 'NSM','STATUS', 'DATETIME');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                //dd($row);
                $datetime = date('j F, Y', strtotime($row['created_at']));
                $displayASEName = '';
                foreach(explode(',',$row->user_id) as $aseKey => $aseVal) 
                {
                    
                    $catDetails = DB::table('users')->where('id', $aseVal)->first();
                    $displayASEName .= $catDetails->name.',';
                }
                $store_name = $row->store_name ?? '';
               
                $storename = Team::where('store_id', $row->id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();

                $lineData = array(
                    $count,
                    ucwords($row->name),
                    ucwords($row->business_name),
                    ucwords($row->address),
                    $row->areas->name,
                    $row->pin,
                    $row->states->name,
                    ucwords($row->owner_fname.' '.$row->owner_lname),
                    $row->contact,
                    $row->whatsapp,
                    $row->contact_person_fname.' '.$row->contact_person_lname,
                    $row->contact_person_phone,
                    $row->date_of_birth,
                    $row->date_of_anniversary,
                    $row->email,
                    $row->gst_no,
                    $storename->distributors->name ?? '',
                    substr($displayASEName, 0, -1) ? substr($displayASEName,0, -1) : 'NA',
                    $storename->ase->name ?? '',
                    $storename->asm->name ?? '',
                    $storename->sm->name ?? '',
                    $storename->rsm->name ?? '',
                    $storename->zsm->name ?? '',
                    $storename->nsm->name ?? '',
                    
                    ($row->status == 1) ? 'Active' : 'Inactive',
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
    
      //user no order reason list
    public function noOrderreason(Request $request)
    {
        if (isset($request->user_id) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm) ||isset($request->store_id) || isset($request->comment) || isset($request->keyword)) {

            $user_id = $request->user_id ? $request->user_id : '';
            $asm=$request->asm ? $request->asm : '';
            $store_id = $request->store_id ? $request->store_id : '';
            $comment = $request->comment ? $request->comment : '';
            $keyword = $request->keyword ? $request->keyword : '';

            $query = UserNoorderreason::query();

            $query->when($user_id, function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
            $query->when($asm, function($query) use ($asm) {
                $query->where('user_id', $asm);
            });
            $query->when($store_id, function($query) use ($store_id) {
                $query->where('store_id', $store_id);
            });
            $query->when($comment, function($query) use ($comment) {
                $query->where('comment', 'like', '%'.$comment.'%');
            });
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('comment', 'like', '%'.$keyword.'%');
            });

            $data = $query->latest('id')->paginate(25);
           
        } else {
            $data = UserNoOrderReason::latest('id')->paginate(25);
        }
        $zsm=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();
        $ases = User::select('id', 'name')->where('type', 6)->orWhere('type', 5)->orderBy('name')->get();
        $stores = Store::select('id', 'name')->where('status',1)->orderBy('name')->get();
        $reasons = NoOrderReason::select('noorderreason')->orderBy('noorderreason')->get();
    
        return view('admin.store.noorder',compact('data', 'ases', 'stores', 'reasons','request','zsm'));
    }
    //csv export of no order reason list
    public function noOrderreasonCSV(Request $request)
    {
        if (isset($request->user_id) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm) ||isset($request->store_id) || isset($request->comment) || isset($request->keyword)) {

            $user_id = $request->ase ? $request->ase : '';
            $asm=$request->asm ? $request->asm : '';
            $store_id = $request->store_id ? $request->store_id : '';
            $comment = $request->comment ? $request->comment : '';
            $keyword = $request->keyword ? $request->keyword : '';

            $query = UserNoorderreason::query();

            $query->when($user_id, function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
            $query->when($asm, function($query) use ($asm) {
                $query->where('user_id', $asm);
            });
            $query->when($store_id, function($query) use ($store_id) {
                $query->where('store_id', $store_id);
            });
            $query->when($comment, function($query) use ($comment) {
                $query->where('comment', 'like', '%'.$comment.'%');
            });
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('comment', 'like', '%'.$keyword.'%');
            });

            $data = $query->latest('id')->get();
            
        } else {
            $data = UserNoOrderReason::latest('id')->get();
        }
        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-no-order-reason-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'STORE', 'USER', 'COMMENT', 'DESCRIPTION', 'LOCATION', 'DATETIME');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                $datetime = date('j F, Y h:i A', strtotime($row['created_at']));

                $store = Store::select('name')->where('id', $row['store_id'])->first();
                $ase = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['user_id'])->first();

                $lineData = array(
                    $count,
                    $store->name ?? '',
                    $ase->name ?? '',
                    $row['comment'],
                    $row['description'],
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
}
