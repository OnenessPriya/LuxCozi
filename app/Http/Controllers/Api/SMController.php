<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Team;
use App\Models\Store;
use App\Models\Activity;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class SMController extends Controller
{
     //inactive ASE report for ASM in dashboard
    public function inactiveAseListSM(Request $request)
    {
         $userId = $_GET['user_id'];
         $aseDetails = Team::select('users.id')->join('users', 'teams.ase_id', '=', 'users.id')->where('teams.sm_id', '=', $userId)->groupby('teams.ase_id')->orderby('teams.ase_id')->get()->pluck('id')->toArray();
                 
         $activeASEreport=Activity::where('type','Visit Started')->whereDate('created_at', '=', Carbon::now())->whereIn('user_id',$aseDetails)->pluck('user_id')->toArray();
                 
         $inactiveASE=Team::select(DB::raw("users.id as id"),DB::raw("users.name as name"),DB::raw("users.mobile as mobile"),DB::raw("users.state as state"),DB::raw("users.city as city"))->join('users', 'teams.ase_id', '=', 'users.id')->where('teams.sm_id', '=', $userId)->whereNotIn('users.id',$activeASEreport)->groupby('teams.ase_id')->orderby('teams.ase_id')->get();
             
         return response()->json(['error' => false, 'resp' => 'Inactive ASE report - Team wise', 'data' => $inactiveASE]);
         
    }

    // store wise team report
    public function storeReportSM(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_id' => ['required'],
            'date_from' => ['nullable'],
            'date_to' => ['nullable'],
            'collection' => ['nullable'],
            'category' => ['nullable'],
            'orderBy' => ['nullable'],
            'style_no' => ['nullable'],
        ]);
         DB::enableQueryLog();
        if (!$validator->fails()) {

            $retailers = Store::select('id','name','address','area_id','state_id','pin')->where('area_id',$request->area_id)->orderby('name')->get();
            $retailerResp = $resp = [];

            foreach($retailers as $retailer) {
                if ( !empty($request->date_from) || !empty($request->date_to) ) {
                    // date from
                    if (!empty($request->date_from)) {
                        $from = date('Y-m-d', strtotime($request->date_from));
                    } else {
                        $from = date('Y-m-01');
                    }

                    // date to
                    if (!empty($request->date_to)) {
                        $to = date('Y-m-d', strtotime($request->date_to));
                    } else {
                        $to = date('Y-m-d', strtotime('+1 day'));
                    }

                    // collection
                    if (!isset($request->collection) || $request->collection == '10000') {
                        $collectionQuery = "";
                    } else {
                        $collectionQuery = " AND p.collection_id = ".$request->collection;
                    }

                    // category
                    if ($request->category == '10000' || !isset($request->category)) {
                        $categoryQuery = "";
                    } else {
                        $categoryQuery = " AND p.cat_id = ".$request->category;
                    }

                    // style no
                    if (!isset($request->style_no)) {
                        $styleNoQuery = "";
                    } else {
                        $styleNoQuery = " AND p.style_no LIKE '%".$request->style_no."%'";
                    }

                    // order by
                    if ($request->orderBy == 'date_asc') {
                        $orderByQuery = "op.id ASC";
                    } elseif ($request->orderBy == 'qty_asc') {
                        $orderByQuery = "qty ASC";
                    } elseif ($request->orderBy == 'qty_desc') {
                        $orderByQuery = "qty DESC";
                    } else {
                        $orderByQuery = "op.id DESC";
                    }

                    $report = DB::select("SELECT IFNULL(SUM(op.qty), 0) AS qty FROM `orders` AS o
                    INNER JOIN order_products AS op ON op.order_id = o.id
                    INNER JOIN products p ON p.id = op.product_id
                    WHERE o.store_id = '".$retailer->id."'
                    ".$collectionQuery."
                    ".$categoryQuery."
                    ".$styleNoQuery."
                    AND (date(o.created_at) BETWEEN '".$from."' AND '".$to."')
                    ORDER BY ".$orderByQuery);
                } else {
                    $report = DB::select("SELECT IFNULL(SUM(op.qty), 0) AS qty FROM `orders` AS o INNER JOIN order_products AS op ON op.order_id = o.id WHERE o.store_id = '".$retailer->id."' AND (date(o.created_at) BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-d', strtotime('+1 day'))."')");
                }

                $retailerResp[] = [
                    'store_id' => $retailer->id,
                    'store_name' => $retailer->name,
                    'address' => $retailer->address,
                    'area' => $retailer->areas->name,
                    'state' => $retailer->states->name,
                    'pin' => $retailer->pin,
                    'quantity' => $report[0]->qty
                ];
            }

            $resp[] = [
                'secondary_sales' => $retailerResp,
            ];

            return response()->json(['error' => false, 'resp' => 'ASM report - Store wise', 'data' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }

    //product wise team report
    public function productReportSM(Request $request)
    {
        \DB::connection()->enableQueryLog();
        $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'date_from' => ['nullable'],
            'date_to' => ['nullable'],
            'collection' => ['nullable'],
            'category' => ['nullable'],
            'orderBy' => ['nullable'],
            'style_no' => ['nullable'],
        ]);

        if (!$validator->fails()) {
            $retailerResp = $resp = [];

            if ( !empty($request->date_from) || !empty($request->date_to) ) {
                // date from
                if (!empty($request->date_from)) {
                    $from = date('Y-m-d', strtotime($request->date_from));
                } else {
                    $from = date('Y-m-01');
                }

                // date to
                if (!empty($request->date_to)) {
                    $to = date('Y-m-d', strtotime($request->date_to));
                } else {
                    $to = date('Y-m-d', strtotime('+1 day'));
                }

                // collection
                if ($request->collection == '10000' || !isset($request->collection)) {
                    $collectionQuery = "";
                } else {
                    $collectionQuery = " AND p.collection_id = ".$request->collection;
                }

                // category
                if ($request->category == '10000' || !isset($request->category)) {
                    $categoryQuery = "";
                } else {
                    $categoryQuery = " AND p.cat_id = ".$request->category;
                }

                // style no
                if (!isset($request->style_no)) {
                    $styleNoQuery = "";
                } else {
                    $styleNoQuery = " AND p.style_no LIKE '%".$request->style_no."%'";
                }

                // order by
                if ($request->orderBy == 'date_asc') {
                    $orderByQuery = "op.id ASC";
                } elseif ($request->orderBy == 'qty_asc') {
                    $orderByQuery = "qty ASC";
                } elseif ($request->orderBy == 'qty_desc') {
                    $orderByQuery = "qty DESC";
                } else {
                    $orderByQuery = "op.id DESC";
                }

                $report = DB::select("SELECT  p.name,op.product_id, p.style_no,IFNULL(SUM(op.qty), 0) AS product_count FROM `order_products` op
                INNER JOIN products p ON p.id = op.product_id
                INNER JOIN orders o ON o.id = op.order_id
                INNER JOIN stores s ON s.id = o.store_id
                INNER JOIN teams t ON s.id = t.store_id
                WHERE t.sm_id = ".$request->user_id."
                AND (DATE(op.created_at) BETWEEN '".$from."' AND '".$to."')
                ".$collectionQuery."
                ".$categoryQuery."
                ".$styleNoQuery."
                GROUP BY op.product_id
                ORDER BY ".$orderByQuery);
                
            } else {
                $report = DB::select("SELECT  p.name,op.product_id, p.style_no, IFNULL(SUM(op.qty), 0) AS product_count FROM `order_products` op
                INNER JOIN products p ON p.id = op.product_id
                INNER JOIN orders o ON o.id = op.order_id
                INNER JOIN stores s ON s.id = o.store_id
                INNER JOIN teams t ON s.id = t.store_id
                WHERE t.sm_id = ".$request->user_id."
                AND (DATE(op.created_at) BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-d', strtotime('+1 day'))."')
                GROUP BY op.product_id
                ORDER BY op.id DESC");
            }

            foreach($report as $item) {
                $retailerResp[] = [
                    'style_no' => $item->style_no,
                    'product' => $item->name,
                    'quantity' => $item->product_count
                ];
            }

			$resp[] = [
				'secondary_sales' => $retailerResp,
			];
         	return response()->json(['error' => false, 'resp' => 'SM report - Product wise', 'data' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }

    //notification list
    public function notificationList(Request $request){
		$validator = Validator::make($request->all(), [
			'user_id' => ['required'],
			'pageNo' => ['nullable'],
		]);
		
		if (!$validator->fails()) {
			$user_id = $request->user_id;
          	$pageNo =$request->pageNo;
			if(!$pageNo){
               $page=1;
             }else{
              $page=$pageNo;
			  }
              $limit=20;
              $offset=($page-1)*$limit;
			  $notifications = DB::select("select * from notifications where receiver_id='$user_id' ORDER BY id desc LIMIT ".$limit." OFFSET ".$offset."");
			  $notificationCount=DB::table('notifications')->where('receiver_id','=',$user_id)->count();
			  $count= (int) ceil($notificationCount / $limit);
				return response()->json(['error' => false, 'message' => 'User wise notification list', 'data' => $notifications,'count'=>$count]);
			
			
		}else{
			return response()->json(['error' => true, 'message' => 'Please send a valid user']);
		}
	}
	//notification update
	public function readNotification(Request $request){
		$id = $request->id;
		$read_time = date("Y-m-d G:i:s");
		
		DB::select("update notifications set read_flag=1, read_at='$read_time' where id='$id'");
		
		return response()->json(['error' => false, 'message' => 'Notification date updated successfully']);
	}
}
