<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Models\State;
use App\Models\Store;
use App\Models\User;
use App\Models\Team;
use App\Models\UserLogin;
use DB;
class OrderController extends Controller
{
    //store wise order report
    public function index(Request $request)
    {
        $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
        if (isset($request->date_from) || isset($request->date_to) || isset($request->term) || isset($request->user_id) || isset($request->store_id)|| isset($request->state_id)|| isset($request->area_id)|| isset($request->distributor_id)) {
            

            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $term = $request->term ? $request->term : '';
            $user_id = $request->user_id ? $request->user_id : '';
            $store_id = $request->store_id ? $request->store_id : '';
            $state_id = $request->state_id ? $request->state_id : '';
            $area_id = $request->area_id ? $request->area_id : '';
            $distributor_id = $request->distributor_id ? $request->distributor_id : '';
            $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.order_type','orders.comment','stores.name','orders.created_at','teams.distributor_id','users.name')->join('users', 'users.id', 'orders.user_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id');

        
            $query->when($user_id, function($query) use ($user_id) {
                $query->where('orders.user_id', $user_id);
            });
            $query->when($store_id, function($query) use ($store_id) {
                $query->where('orders.store_id', $store_id);
            });
            $query->when($state_id, function($query) use ($state_id) {
                $query->where('stores.state_id', $state_id);
            });
            $query->when($area_id, function($query) use ($area_id) {
                $query->where('stores.area_id', $area_id);
            });
            $query->when($distributor_id, function($query) use ($distributor_id) {
                $query->join('users', 'users.id', 'teams.distributor_id')->where('users.id', $distributor_id);
            });
            $query->when($query, function($query) use ($term) {
                $query->where('orders.order_no', 'like', '%'.$term.'%');
            })->whereBetween('orders.created_at', [$date_from, $date_to]);

            $data = $query->latest('orders.id')->paginate(25);
            
        } else {
            $data = Order::orderBy('id', 'desc')->latest('id')->paginate(25);
        }
        $user = User::select('id', 'name')->where('type', 6)->orWhere('type',5)->where('status',1)->orderBy('name')->get();
        $stores = Store::select('id', 'name')->where('status',1)->orderBy('name')->get();
        $state = State::where('status',1)->groupBy('name')->orderBy('name')->get();
        $distributor = User::select('id', 'name')->where('type', 7)->where('status',1)->orderBy('name')->get();
        return view('admin.order.store-report', compact('data','request','user','stores','state','distributor'));
    }

    //pdf download for individual order
    public function pdfExport(Request $request, $id)
    {
        $data = Order::findOrfail($id);
        return view('admin.order.pdf', compact('data'));
    }

    //csv download for individual order
    public function individualcsvExport(Request $request, $id)
    {
        $orderDetails = Order::findOrfail($id);
        $data = orderProductsUpdatedMatrix($orderDetails->orderProducts);
        $childData = orderProductsUpdatedMatrixChild($orderDetails->orderProducts);

        if (count($data) > 0 || count($childData) > 0) {
            $delimiter = ",";
            $filename = "lux-secondary-order-detail-".$orderDetails->order_no."-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('Name of Quality Shape & Unit', '75', '80', '85', '90', '95', '100', '105', '110', '115','120', 'Total');
            $childFields = array('Name of Quality Shape & Unit', '35', '40', '45', '50', '55', '60', '65', '70', '75','','Total');

            $count = 1;

            if (count($data) > 0) {
                fputcsv($f, $fields, $delimiter);
                foreach($data as $row) {
					 
                     $row1 = $row['product_name']."\n".$row['product_style_no']."\n".$row['color'];

                    $lineData = array(
                        $row1,
                        $row['75'] ? $row['75'] : '',
                        $row['80'] ? $row['80'] : '',
                        $row['85'] ? $row['85'] : '',
                        $row['90'] ? $row['90'] : '',
                        $row['95'] ? $row['95'] : '',
                        $row['100'] ? $row['100'] : '',
                        $row['105'] ? $row['105'] : '',
                        $row['110'] ? $row['110'] : '',
                        $row['115'] ? $row['115'] : '',
                        $row['120'] ? $row['120'] : '',
                        $row['total']
                    );
                    fputcsv($f, $lineData, $delimiter);
                    $count++;
                }
            }

            if (count($childData) > 0) {
                fputcsv($f, $childFields, $delimiter);
                foreach($childData as $row) {
					 
                    $row2 = $row['product_name']."\n".$row['product_style_no']."\n".$row['color'];

                    $lineData = array(
                        $row2,
                        $row['35'] ? $row['35'] : '',
                        $row['40'] ? $row['40'] : '',
                        $row['45'] ? $row['45'] : '',
                        $row['50'] ? $row['50'] : '',
                        $row['55'] ? $row['55'] : '',
                        $row['60'] ? $row['60'] : '',
                        $row['65'] ? $row['65'] : '',
                        $row['70'] ? $row['70'] : '',
                        $row['75'] ? $row['75'] : '',
						'',
                        $row['total']
                    );

                    fputcsv($f, $lineData, $delimiter);
                    $count++;
                }
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

    //all order csv export
    public function csvExport(Request $request)
    {
        $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
        if (isset($request->date_from) || isset($request->date_to) || isset($request->term) || isset($request->user_id) || isset($request->store_id) ) {
            

            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $term = $request->term ? $request->term : '';
            $user_id = $request->user_id ? $request->user_id : '';
            $store_id = $request->store_id ? $request->store_id : '';

            $query = Order::join('order_products', 'orders.id', '=', 'order_products.order_id');
            $query->when($user_id, function($query) use ($user_id) {
                $query->where('orders.user_id', $user_id);
            });
            $query->when($store_id, function($query) use ($store_id) {
                $query->where('orders.store_id', $store_id);
            });
            $query->when($query, function($query) use ($term) {
                $query->where('orders.order_no', 'like', '%'.$term.'%');
            })->whereBetween('orders.created_at', [$date_from, $date_to]);

            $data = $query->latest('orders.id')->paginate(25);

        } else {
            $data = Order::join('order_products', 'orders.id', '=', 'order_products.order_id')->orderBy('orders.id', 'desc')->latest('orders.id')->paginate(25);
           
        }
        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-secondary-order-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'ORDER NO', 'STORE','STORE STATE','STORE AREA','DISTRIBUTOR', 'SALES PERSON(ASE/ASM)', 'MOBILE', 'STATE', 'CITY', 'PINCODE', 'PRODUCT', 'STYLE NO', 'COLOR', 'SIZE', 'QTY', 'DATETIME');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
				
                $datetime = date('j F, Y h:i A', strtotime($row['created_at']));
                $productDetails=Product::where('id',$row->product_id)->with('collection','category')->first();
                $color=Color::where('id',$row['color_id'])->first();
                $size=Size::where('id',$row['size_id'])->first();
                $user=Team::where('store_id',$row->store_id)->first();
                $userName=User::where('id',$user->distributor_id)->first();
                $lineData = array(
                    $count,
                    $row['order_no'],
                    $row->stores->name ?? '',
                    $row->stores->states->name ?? '',
                    $row->stores->areas->name ?? '',
                    $userName->name ?? '',
                    $row->users->name ?? 'Self Order',
                    $row->users->mobile ?? '',
                    $row->users->state ?? '',
                    $row->users->city ?? '',
                    $row->users->pin ?? '',
                    $productDetails->name?? '',
                    $productDetails->style_no?? '',
                    $color->name,
                    $size->name,
                    $row['qty'],
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

    //product wise sales report
    public function productwiseOrder(Request $request)
    { 
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
            ->join('orders', 'orders.id', 'order_products.order_id');
            $query1->when($ase, function($query1) use ($ase) {
                $query1->join('users', 'users.id', 'orders.user_id')->where('users.id', $ase);
            });
            $query1->when($product, function($query1) use ($product) {
                $query1->where('order_products.product_id', $product);
            });
            $query1->when($state, function($query1) use ($state) {
                $query1->join('stores', 'stores.id', 'orders.store_id')->where('stores.state_id', $state);
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
            ->join('orders', 'orders.id', 'order_products.order_id')->whereBetween('order_products.created_at', [$from, $to])->with('color','size')->latest('orders.id')->paginate(50);
           
       }
        $allASEs = User::select('id','name')->where('type',5)->orWhere('type',6)->where('name', '!=', null)->orderBy('name')->get();
      	$allStores = Store::select('id', 'name')->where('status',1)->orderBy('name')->get();
        $state = State::where('status',1)->groupBy('name')->orderBy('name')->get();
        $data->products = Product::where('status', 1)->orderBy('name')->get();
        return view('admin.order.product-order', compact('data','allASEs','state','request','allStores'));
    }

    //product wise order report csv download
    public function productcsvExport(Request $request)
    {
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
            ->join('orders', 'orders.id', 'order_products.order_id');
            $query1->when($ase, function($query1) use ($ase) {
                $query1->join('users', 'users.id', 'orders.user_id')->where('users.id', $ase);
            });
            $query1->when($product, function($query1) use ($product) {
                $query1->where('order_products.product_id', $product);
            });
            $query1->when($state, function($query1) use ($state) {
                $query1->join('stores', 'stores.id', 'orders.store_id')->where('stores.state_id', $state);
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
            ->join('orders', 'orders.id', 'order_products.order_id')->whereBetween('order_products.created_at', [$from, $to])->with('color','size')->latest('orders.id')->paginate(50);
           
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
	//area wise sales report
    public function areawiseOrder(Request $request)
    {
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
            $query1 = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("areas.name as area"),DB::raw("states.name as state"))->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('areas', 'stores.area_id', 'areas.id')->join('states', 'stores.state_id', 'states.id');
            
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
            $data->all_orders = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("areas.name as area"),DB::raw("states.name as state"))->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('states', 'stores.state_id', 'states.id')->join('areas', 'stores.area_id', 'areas.id')->whereBetween('orders.created_at', [$from, $to])->groupby('stores.area_id')->paginate(50);
            
        }
        $state = State::where('status',1)->groupBy('name')->orderBy('name')->get();
        return view('admin.order.area-order', compact('data','state','request'));
    }

        //area wise order report csv download
        public function areacsvExport(Request $request)
        {
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
                $query1 = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("areas.name as area"),DB::raw("states.name as state"))->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('areas', 'stores.area_id', 'areas.id')->join('states', 'stores.state_id', 'states.id');
                
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
                $data->all_orders = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("areas.name as area"),DB::raw("states.name as state"))->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('states', 'stores.state_id', 'states.id')->join('areas', 'stores.area_id', 'areas.id')->whereBetween('orders.created_at', [$from, $to])->groupby('stores.area_id')->paginate(50);
                
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

        //category wise sales report
        public function categorywiseOrder(Request $request)
        {
            $data = (object) [];
            $from =  date('Y-m-01');
            $to =  date('Y-m-d', strtotime('+1 day'));
            if(isset($request->date_from) || isset($request->date_to)) 
            {
                $from = $request->date_from ? $request->date_from : date('Y-m-01');
                $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
                $state = $request->state_id ?? '';
                $area = $request->area_id ?? '';
                // all order products
                $query1 = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("categories.name as category"),DB::raw("users.name as name"))->join('orders', 'orders.id', 'order_products.order_id')->join('users', 'users.id', 'orders.user_id')->join('products', 'products.id', 'order_products.product_id')->join('categories', 'categories.id', 'products.cat_id')
                
                ->whereBetween('order_products.created_at', [$from, $to]);
                $data->all_orders = $query1->groupby('products.cat_id')
                ->paginate(50);
               
            }else{
                $data->all_orders = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("categories.name as category"),DB::raw("users.name as name"))->join('orders', 'orders.id', 'order_products.order_id')->join('users', 'users.id', 'orders.user_id')->join('products', 'products.id', 'order_products.product_id')->join('categories', 'categories.id', 'products.cat_id')
                
                ->whereBetween('order_products.created_at', [$from, $to])->groupby('products.cat_id')->paginate(50);
               
            }
            $category = Category::where('status',1)->groupBy('name')->orderBy('name')->get();
            return view('admin.order.category-order', compact('data','category','request'));
        }

          //category wise sales report
          public function categorycsvExport(Request $request)
          {
              $data = (object) [];
              $from =  date('Y-m-01');
              $to =  date('Y-m-d', strtotime('+1 day'));
              if(isset($request->date_from) || isset($request->date_to)) 
              {
                  $from = $request->date_from ? $request->date_from : date('Y-m-01');
                  $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
                  $state = $request->state_id ?? '';
                  $area = $request->area_id ?? '';
                  // all order products
                  $query1 = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("categories.name as category"),DB::raw("users.name as name"))->join('orders', 'orders.id', 'order_products.order_id')->join('users', 'users.id', 'orders.user_id')->join('products', 'products.id', 'order_products.product_id')->join('categories', 'categories.id', 'products.cat_id')
                  
                  ->whereBetween('order_products.created_at', [$from, $to]);
                  $data->all_orders = $query1->groupby('orders.id')
                  ->paginate(50);
                 
              }else{
                  $data->all_orders = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("categories.name as category"),DB::raw("users.name as name"))->join('orders', 'orders.id', 'order_products.order_id')->join('users', 'users.id', 'orders.user_id')->join('products', 'products.id', 'order_products.product_id')->join('categories', 'categories.id', 'products.cat_id')
                  
                  ->whereBetween('order_products.created_at', [$from, $to])->groupby('orders.id')->paginate(50);
                 
              }
              
            if (count($data->all_orders) > 0) {
                $delimiter = ",";
                $filename = "lux-category-wise-sales-report-".date('Y-m-d').".csv";
    
                // Create a file pointer 
                $f = fopen('php://memory', 'w');
    
                // Set column headers 
                $fields = array('SR', 'EMPLOYEE', 'CATEGORY','QUANTITY');
                fputcsv($f, $fields, $delimiter); 
    
                $count = 1;
    
                foreach($data->all_orders as $row) {
                   
                   
                    $lineData = array(
                        $count,
                        $row['name'] ?? '',
                        $row['category'] ?? '',
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
	 //login report
    public function loginReport(Request $request)
    {
        if (isset($request->ase) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm) ||  isset($request->date_from)|| isset($request->date_to)) {

            $ase = $request->ase ? $request->ase : '';
            $asm=$request->asm ? $request->asm : '';
            $sm=$request->sm ? $request->sm : '';
            $rsm=$request->rsm ? $request->rsm : '';
            $zsm=$request->zsm ? $request->zsm : '';
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $query = UserLogin::query();

            $query->when($ase, function($query) use ($ase) {
                $query->where('user_id', $ase);
            });
            $query->when($asm, function($query) use ($asm) {
                $query->where('user_id', $asm);
            });
            $query->when($rsm, function($query) use ($rsm) {
                $query->where('user_id', $rsm);
            });
            $query->when($sm, function($query) use ($sm) {
                $query->where('user_id', $sm);
            });
            $query->when($zsm, function($query) use ($zsm) {
                $query->where('user_id', $zsm);
            });

            $data = $query->latest('id')->with('users')->paginate(25);
           
        } else {
            $data = UserLogin::latest('id')->with('users')->paginate(25);
           
        }
        $zsm=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();
        $ases = User::select('id', 'name')->where('type', 6)->orWhere('type', 5)->orderBy('name')->get();
        
    
        return view('admin.report.login-report',compact('data', 'ases','request','zsm'));
    }
	
	  //csv download
    public function loginReportcsvExport(Request $request)
    {
        if (isset($request->ase) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm) ||  isset($request->date_from)|| isset($request->date_to)) {

            $ase = $request->ase ? $request->ase : '';
            $asm=$request->asm ? $request->asm : '';
            $sm=$request->sm ? $request->sm : '';
            $rsm=$request->rsm ? $request->rsm : '';
            $zsm=$request->zsm ? $request->zsm : '';
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $query = UserLogin::query();

            $query->when($ase, function($query) use ($ase) {
                $query->where('user_id', $ase);
            });
            $query->when($asm, function($query) use ($asm) {
                $query->where('user_id', $asm);
            });
            $query->when($rsm, function($query) use ($rsm) {
                $query->where('user_id', $rsm);
            });
            $query->when($sm, function($query) use ($sm) {
                $query->where('user_id', $sm);
            });
            $query->when($zsm, function($query) use ($zsm) {
                $query->where('user_id', $zsm);
            });

            $data = $query->latest('id')->with('users')->paginate(25);
           
        } else {
            $data = UserLogin::latest('id')->with('users')->paginate(25);
           
        }
        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-login-report-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'NSM', 'ZSM','RSM','SM','ASM','Employee','Employee Id','Employee Status','Employee Designation','Employee Date of Joining','Employee HQ','Employee Contact No','Login Status',  'Time');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                $datetime = date('j F, Y h:i A', strtotime($row['created_at']));
                if($row->is_login==''){
                    $is_login='Inactive';
                   
                }else{
                     $is_login= 'Logged In';
                }
                $store = Store::select('name')->where('id', $row['store_id'])->first();
                $ase = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['user_id'])->first();
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
                    $is_login ?? '',
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

