<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Cart;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;
use DB;
class ReportController extends Controller
{
    //for ASE store wise report
    public function storeReportASE(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ase_id' => ['required'],
            'date_from' => ['nullable'],
            'date_to' => ['nullable'],
            'collection' => ['nullable'],
            'category' => ['nullable'],
            'orderBy' => ['nullable'],
            'style_no' => ['nullable'],
        ]);
         DB::enableQueryLog();
        if (!$validator->fails()) {
            $userName = User::findOrFail($request->ase_id);
            $userName = $userName->name;

            $retailers = Store::select('id','name','address','area_id','state_id','pin')->where('user_id',$request->ase_id)->orderby('name')->get();
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

            return response()->json(['error' => false, 'resp' => 'ASE report - Store wise', 'data' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }

     //for ASE product wise report
     public function productReportASE(Request $request)
    {
        \DB::connection()->enableQueryLog();
        $validator = Validator::make($request->all(), [
            'ase_id' => ['required'],
            'date_from' => ['nullable'],
            'date_to' => ['nullable'],
            'collection' => ['nullable'],
            'category' => ['nullable'],
            'orderBy' => ['nullable'],
            'style_no' => ['nullable'],
        ]);

        if (!$validator->fails()) {
            $userName = User::findOrFail($request->ase_id);
            $userName = $userName->name;

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
                WHERE s.user_id = ".$request->ase_id."
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
                WHERE s.user_id = ".$request->ase_id."
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
         	return response()->json(['error' => false, 'resp' => 'ASE report - Product wise', 'data' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }
}
