<?php

namespace App\Http\Controllers\Front;

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
class ReportController extends Controller
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
        return view('front.store-report.index', compact('data','request','user','stores','state','distributor'));
    }

    //pdf download for individual order
    public function pdfExport(Request $request, $id)
    {
        $data = Order::findOrfail($id);
        return view('front.store-report.pdf', compact('data'));
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
            $fields = array('SR', 'ORDER NO','ORDER TYPE', 'STORE','STORE STATE','STORE AREA','DISTRIBUTOR', 'SALES PERSON(ASE/ASM)', 'MOBILE', 'STATE', 'CITY', 'PINCODE', 'PRODUCT', 'STYLE NO', 'COLOR', 'SIZE', 'QTY', 'DATETIME');
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
                    $row['order_type'],
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

    
	
}

