<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Category;
use App\Models\State;
use App\Models\Activity;
use App\Models\Notification;
use App\Models\Store;
use App\Models\Team;

use Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    // private UserRepository $userRepository;

	public function notificationRead(Request $request)
    {
        $noti = Notification::findOrFail($request->id);
        $noti->read_flag = 1;
        $noti->save();
    }
	
	 public function order(Request $request)
    {
     
        return view('front.profile.order');
    }
	
	 public function list(Request $request)
    {
		 $loggedInUserId = Auth::guard('web')->user()->id;
		 $asms=Team::select('asm_id')->where('zsm_id',$loggedInUserId)->groupby('asm_id')->with('asm')->get();
        $ases=Team::select('ase_id')->where('zsm_id',$loggedInUserId)->groupby('ase_id')->with('ase')->get();
        return view('front.user.index',compact('ases','asms'));
    }

		//area wise sales report
    public function areaorder(Request $request)
    {
		$loggedInUserId = Auth::guard('web')->user()->id;
        $data = (object) [];
        $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
        if(isset($request->date_from) || isset($request->date_to) || isset($request->state_id)||isset($request->area_id)) 
		{
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $state = $request->state_id ?? '';
            $area = $request->area_id ?? '';
            // all order products
            $query1 = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("areas.name as area"),DB::raw("states.name as state"))->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id')->join('areas', 'stores.area_id', 'areas.id')->join('states', 'stores.state_id', 'states.id')->where('teams.zsm_id',$loggedInUserId);
            
            $query1->when($state, function($query1) use ($state) {
                $query1->where('stores.state_id', $state);
            });
            $query1->when($area, function($query1) use ($area) {
                $query1->where('stores.area_id', $area);
            })
			->whereBetween('order_products.created_at', [$from, $to]);
            $data->all_orders = $query1->groupby('stores.area_id')
            ->paginate(50);
           
        }else{
            $data->all_orders = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("areas.name as area"),DB::raw("states.name as state"))->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id')->join('states', 'stores.state_id', 'states.id')->join('areas', 'stores.area_id', 'areas.id')->where('teams.zsm_id',$loggedInUserId)->whereBetween('orders.created_at', [$from, $to])->groupby('stores.area_id')->paginate(50);
            
        }
        $state = Team::where('zsm_id',$loggedInUserId)->groupBy('state_id')->with('states')->get();
        return view('front.area.index', compact('data','state','request'));
    }

        //area wise order report csv download
        public function areaorderCsv(Request $request)
        {
           $loggedInUserId = Auth::guard('web')->user()->id;
        $data = (object) [];
        $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
        if(isset($request->date_from) || isset($request->date_to) || isset($request->state_id)||isset($request->area_id)) 
		{
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $state = $request->state_id ?? '';
            $area = $request->area_id ?? '';
            // all order products
            $query1 = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("areas.name as area"),DB::raw("states.name as state"))->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id')->join('areas', 'stores.area_id', 'areas.id')->join('states', 'stores.state_id', 'states.id')->where('teams.zsm_id',$loggedInUserId);
            
            $query1->when($state, function($query1) use ($state) {
                $query1->where('stores.state_id', $state);
            });
            $query1->when($area, function($query1) use ($area) {
                $query1->where('stores.area_id', $area);
            })
			->whereBetween('order_products.created_at', [$from, $to]);
            $data->all_orders = $query1->groupby('stores.area_id')
            ->paginate(50);
           
        }else{
            $data->all_orders = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("areas.name as area"),DB::raw("states.name as state"))->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id')->join('states', 'stores.state_id', 'states.id')->join('areas', 'stores.area_id', 'areas.id')->where('teams.zsm_id',$loggedInUserId)->whereBetween('orders.created_at', [$from, $to])->groupby('stores.area_id')->paginate(50);
            
        }
    
            if (count($data->all_orders) > 0) {
                $delimiter = ",";
                $filename = "lux-area-wise-sales-report-".date('Y-m-d').".csv";
    
                // Create a file pointer 
                $f = fopen('php://memory', 'w');
    
                // Set column headers 
                $fields = array('SR', 'AREA', 'STATE','QUANTITY');
                fputcsv($f, $fields, $delimiter); 
    
                $count = 1;
    
                foreach($data->all_orders as $row) {
                   
                   
                    $lineData = array(
                        $count,
                        $row['area'] ?? '',
                        $row['state'] ?? '',
                        $row['qty'] ?? '',
                       
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
	
	//product wise sales report
    public function productorder(Request $request)
    { 
		$loggedInUserId = Auth::guard('web')->user()->id;
        $data = (object) [];
        $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
        if(isset($request->date_from) || isset($request->date_to) || isset($request->orderNo)||isset($request->store_id)||isset($request->user_id)||isset($request->state_id)||isset($request->product_id)||isset($request->area_id)) 
		{
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $orderNo = $request->orderNo ? $request->orderNo : '';
            $product = $request->product_id ?? '';
            $state = $request->state_id ?? '';
            $area = $request->area_id ?? '';
            $ase = $request->user_id ?? '';
 			$store_id = $request->store_id ? $request->store_id : '';
            // all order products
            $query1 = OrderProduct::join('products', 'products.id', 'order_products.product_id')
            ->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id')->join('users', 'users.id', 'orders.user_id')->where('teams.zsm_id',$loggedInUserId);
            $query1->when($ase, function($query1) use ($ase) {
                $query1->where('users.id', $ase);
            });
            $query1->when($product, function($query1) use ($product) {
                $query1->where('order_products.product_id', $product);
            });
            $query1->when($state, function($query1) use ($state) {
                $query1->where('stores.state_id', $state);
            });
            $query1->when($area, function($query1) use ($area) {
                $query1->where('stores.area_id', $area);
            });
			$query1->when($store_id, function($query1) use ($store_id) {
                $query1->where('orders.store_id', $store_id);
            });
            $query1->when($orderNo, function($query1) use ($orderNo) {
                $query1->Where('orders.order_no', 'like', '%' . $orderNo . '%');
            })->whereBetween('order_products.created_at', [$from, $to]);

            $data->all_orders = $query1->latest('orders.id')
            ->paginate(50);
           
       }else{
            $data->all_orders = OrderProduct::join('products', 'products.id', 'order_products.product_id')
            ->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id')->where('teams.zsm_id',$loggedInUserId)->whereBetween('order_products.created_at', [$from, $to])->with('color','size')->latest('orders.id')->paginate(50);
           
       }
        
      	$allStores = Store::select('stores.id', 'stores.name')->join('teams', 'stores.id', 'teams.store_id')->where('teams.zsm_id',$loggedInUserId)->where('stores.status',1)->orderBy('stores.name')->get();
        $state = Team::where('zsm_id',$loggedInUserId)->groupBy('state_id')->with('states')->get();
        $data->products = Product::where('status', 1)->orderBy('name')->get();
        return view('front.product.index', compact('data','state','request','allStores'));
    }

    //product wise order report csv download
    public function productorderCsv(Request $request)
    {
       		$loggedInUserId = Auth::guard('web')->user()->id;
        $data = (object) [];
        $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
        if(isset($request->date_from) || isset($request->date_to) || isset($request->orderNo)||isset($request->store_id)||isset($request->user_id)||isset($request->state_id)||isset($request->product_id)||isset($request->area_id)) 
		{
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $orderNo = $request->orderNo ? $request->orderNo : '';
            $product = $request->product_id ?? '';
            $state = $request->state_id ?? '';
            $area = $request->area_id ?? '';
            $ase = $request->user_id ?? '';
 			$store_id = $request->store_id ? $request->store_id : '';
            // all order products
            $query1 = OrderProduct::join('products', 'products.id', 'order_products.product_id')
            ->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id')->join('users', 'users.id', 'orders.user_id')->where('teams.zsm_id',$loggedInUserId);
            $query1->when($ase, function($query1) use ($ase) {
                $query1->where('users.id', $ase);
            });
            $query1->when($product, function($query1) use ($product) {
                $query1->where('order_products.product_id', $product);
            });
            $query1->when($state, function($query1) use ($state) {
                $query1->where('stores.state_id', $state);
            });
            $query1->when($area, function($query1) use ($area) {
                $query1->where('stores.area_id', $area);
            });
			$query1->when($store_id, function($query1) use ($store_id) {
                $query1->where('orders.store_id', $store_id);
            });
            $query1->when($orderNo, function($query1) use ($orderNo) {
                $query1->Where('orders.order_no', 'like', '%' . $orderNo . '%');
            })->whereBetween('order_products.created_at', [$from, $to]);

            $data->all_orders = $query1->latest('orders.id')
            ->paginate(50);
           
       }else{
            $data->all_orders = OrderProduct::join('products', 'products.id', 'order_products.product_id')
            ->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id')->where('teams.zsm_id',$loggedInUserId)->whereBetween('order_products.created_at', [$from, $to])->with('color','size')->latest('orders.id')->paginate(50);
           
       }

        if (count($data->all_orders) > 0) {
            $delimiter = ",";
            $filename = "lux-secondary-order-report-".date('Y-m-d').".csv";

            // Create a file pointer 
            $f = fopen('php://memory', 'w');

            // Set column headers 
            $fields = array('SR', 'ORDER NUMBER', 'PRODUCT STYLE NO','PRODUCT NAME', 'COLOR', 'SIZE','QUANTITY', 'SALES PERSON(ASE/ASM)', 
            'STATE', 'AREA', 'STORE','DATETIME');
            fputcsv($f, $fields, $delimiter); 

            $count = 1;

            foreach($data->all_orders as $row) {
               
                $datetime = date('j M Y g:i A', strtotime($row['orders']['created_at']));
				   
                $lineData = array(
                    $count,
                    $row['order_no'] ?? '',
                    $row['style_no'] ?? '',
                    $row['name'] ?? '',
                    $row['color']['name'] ?? '',
                    $row['size']['name'] ?? '',
                    $row['qty'] ?? '',
                    $row['orders']['users']['name'] ?? '',
                    $row['orders']['stores']['states']['name'] ?? '',
                    $row['orders']['stores']['areas']['name'] ?? '',
                    $row['orders']['stores']['name'] ?? '',
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
	
	
	 public function activityList(Request $request)
    {
        $loggedInUserId = Auth::guard('web')->user()->id;
        $loggedInUser = Auth::guard('web')->user()->name;
        $loggedInUserType = Auth::guard('web')->user()->user_type;
        $ases = DB::select("SELECT u.id as ase_id, u.name as name FROM `teams` t INNER JOIN users u ON u.id = t.ase_id where t.zsm_id = '".$loggedInUserId."' GROUP BY t.ase_id ");		
		 $asms = DB::select("SELECT u.id as ase_id, u.name as name FROM `teams` t INNER JOIN users u ON u.name = t.asm_id where t.zsm_id = '".$loggedInUserId."' GROUP BY t.asm_id ");		
        //dd($ases);
        foreach($ases as $ase){				
            if (!empty(request()->input('from'))) {
                $from = request()->input('from');
            } else {
                $from = $first_day_this_month = date('Y-m-01');
            }

            if (!empty(request()->input('to'))) {
            // $to = date('Y-m-d', strtotime(request()->input('to'). '+1 days'));
                $to = date('Y-m-d', strtotime(request()->input('to')));
                //dd($to);
            } else {
                $to = $current_day_this_month = date('Y-m-d', strtotime('+0 days'));
                //dd($to);
            }

            if ( request()->input('from') || request()->input('to') ) {
                if (!empty(request()->input('from'))) {
                    $from = request()->input('from');
                } else {
                    $from = date('Y-m-01');
                }
                // date to
                if (!empty(request()->input('to'))) {
                    $to = date('Y-m-d', strtotime(request()->input('to')));
                } else {
                    $to = date('Y-m-d', strtotime('+1 day'));
                }
                   $activity = Activity::where('user_id',$ase->ase_id)->orWhere('created_at', $to)->latest('id','desc')->paginate(20);
               // dd($ase->ase_id);
            }
            else{
                $activity = Activity::where('user_id',$ase->ase_id)->whereBetween('created_at', [$from, $to])->latest('id','desc')->paginate(20);
                //dd($to);
            }
        }
		 
		  foreach($asms as $asm){				
            if (!empty(request()->input('from'))) {
                $from = request()->input('from');
            } else {
                $from = $first_day_this_month = date('Y-m-01');
            }

            if (!empty(request()->input('to'))) {
            // $to = date('Y-m-d', strtotime(request()->input('to'). '+1 days'));
                $to = date('Y-m-d', strtotime(request()->input('to')));
                //dd($to);
            } else {
                $to = $current_day_this_month = date('Y-m-d', strtotime('+0 days'));
                //dd($to);
            }

            if ( request()->input('from') || request()->input('to') ) {
                if (!empty(request()->input('from'))) {
                    $from = request()->input('from');
                } else {
                    $from = date('Y-m-01');
                }
                // date to
                if (!empty(request()->input('to'))) {
                    $to = date('Y-m-d', strtotime(request()->input('to')));
                } else {
                    $to = date('Y-m-d', strtotime('+1 day'));
                }
                   $asmactivity = Activity::where('user_id',$asm->asm_id)->orWhere('created_at', $to)->latest('id','desc')->paginate(20);
               // dd($ase->ase_id);
            }
            else{
                $asmactivity = Activity::where('user_id',$asm->asm_id)->whereBetween('created_at', [$from, $to])->latest('id','desc')->paginate(20);
                //dd($to);
            }
        }
		 
		//dd($activity);
        return view('front.activity.index', compact('activity','ases','asms','request'));
		
		
    }
	
	 //store list

    public function storeList(Request $request)
    {
        $loggedInUserId = Auth::guard('web')->user()->id;
        $category=Category::orderby('name')->get();
        $user=User::where('type',5)->orWhere('type',6)->orderby('name')->get();
        $store = Store::join('teams', 'teams.store_id', '=', 'stores.id')->where('teams.zsm_id','=',$loggedInUserId)->get();
        if (!empty(request()->input('from'))) {
            $from = request()->input('from');
        } else {
            $from = $first_day_this_month = date('Y-m-01');
        }

        if (!empty(request()->input('to'))) {
            $to = date('Y-m-d', strtotime(request()->input('to')));
        } else {
            $to = $current_day_this_month = date('Y-m-d', strtotime('+1 day'));
        }
        return view('front.store.index', compact('store','request','loggedInUserId','category','from','to','user'));
    }
	
	
	    //store list
    public function storeApproveList(Request $request)
    {
        $loggedInUserId = Auth::guard('web')->user()->id;
        $allASEs = User::join('teams', 'teams.ase_id', '=', 'users.id')->select('users.id','users.name')->where('teams.zsm_id',$loggedInUserId)->where('users.type',6)->where('users.name', '!=', null)->where('users.status',1)->groupBy('users.name')->orderBy('users.name')->get();
        $allASMs = User::join('teams', 'teams.asm_id', '=', 'users.id')->select('users.id','users.name')->where('teams.zsm_id',$loggedInUserId)->where('users.type',5)->where('users.name', '!=', null)->where('users.status',1)->groupBy('users.name')->orderBy('users.name')->get();
        $allDistributors = User::join('teams', 'teams.distributor_id', '=', 'users.id')->select('users.id','users.name')->where('teams.zsm_id',$loggedInUserId)->where('users.type',7)->where('users.name', '!=', null)->where('users.status',1)->groupBy('users.name')->orderBy('users.name')->get();
        $state = State::selectRaw('states.*')->join('teams', 'teams.state_id', '=', 'states.id')->where('teams.zsm_id',$loggedInUserId)->where('states.status',1)->groupBy('states.name')->orderBy('states.name')->get();
		
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

            $data = $query->where('teams.zsm_id',$loggedInUserId)->where('stores.user_id','!=','')->latest('stores.id')->paginate(25);
            
        }
        else{
            $data = Store::selectRaw('stores.*')->join('teams', 'teams.store_id', 'stores.id')->where('teams.zsm_id',$loggedInUserId)->where('stores.user_id','!=','')
            ->with('states','areas','users')->latest('id')->paginate(25);
            //dd($data);
        }
        
        return view('front.store.list', compact('data','request', 'allASEs', 'allASMs','allDistributors', 'state', 'request','inactiveStore'));
    }

    public function storeDetail($id)
    {
        $data = (object)[];
        $data->stores = Store::where('id',$id)->with('users','states','areas')->first();
        $data->team = Team::where('store_id', $id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();
        $data->users = User::all();
        return view('front.store.details', compact('data'));
    }
	
	
	 public function storeEdit($id)
    {
		 $loggedInUserId = Auth::guard('web')->user()->id;
        $data = (object)[];
        $data->stores = Store::with('users','states','areas')->findOrfail($id);
        $data->states=State::where('status',1)->groupby('name')->orderby('name')->get();
        $data->team = Team::where('store_id', $id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();
       // $data->users = User::where('type',6)->orWhere('type',5)->where('name', '!=', NULL)->where('status',1)->groupBy('name')->orderBy('name')->get();
		$data->users = User::join('teams', 'teams.ase_id', '=', 'users.id')->select('users.id','users.name')->where('teams.zsm_id',$loggedInUserId)->where('users.type',6)->orWhere('users.type',5)->where('users.name', '!=', null)->where('users.status',1)->groupBy('users.name')->orderBy('users.name')->get();
        $data->asms = User::where('type',5)->where('name', '!=', NULL)->where('status',1)->groupBy('name')->orderBy('name')->get();
        //$data->allDistributors = User::select('id','name')->where('type',7)->where('name', '!=', NULL)->where('status',1)->groupBy('name')->orderBy('name')->get();
		 $data->allDistributors = User::join('teams', 'teams.distributor_id', '=', 'users.id')->select('users.id','users.name')->where('teams.zsm_id',$loggedInUserId)->where('users.type',7)->where('users.name', '!=', null)->where('users.status',1)->groupBy('users.name')->orderBy('users.name')->get();
        return view('front.store.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function storeUpdate(Request $request, $id)
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
            'city' => 'nullable',
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
        $store->city = $request->city;
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

    public function storeApproveStatus(Request $request, $id)
    {
        $category = Store::findOrFail($id);
        $status = ( $category->zsm_approval == 1 ) ? 0 : 1;
        $category->zsm_approval = $status;
        $category->save();
        if ($category) {
           return redirect()->back()->with('success','Status updated successfully');
        } else {
            return redirect()->route('front.store.list.approve')->withInput($request->all());
        }
    }
   
}
