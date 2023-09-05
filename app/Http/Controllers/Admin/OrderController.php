<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
class OrderController extends Controller
{
    //store wise order report
    public function index(Request $request)
    {
        $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
        if (isset($request->date_from) || isset($request->date_to) || isset($request->term) || isset($request->user_id) || isset($request->store_id) ) {
            

            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $term = $request->term ? $request->term : '';
            $user_id = $request->user_id ? $request->user_id : '';
            $store_id = $request->store_id ? $request->store_id : '';

            $query = Order::query();

        
            $query->when($user_id, function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
            $query->when($store_id, function($query) use ($store_id) {
                $query->where('store_id', $store_id);
            });
            $query->when($query, function($query) use ($term) {
                $query->where('order_no', 'like', '%'.$term.'%');
            })->whereBetween('created_at', [$date_from, $date_to]);

            $data = $query->latest('id')->paginate(25);

        } else {
            $data = Order::orderBy('id', 'desc')->latest('id')->paginate(25);
        }
        $user = User::select('id', 'name')->where('type', 6)->orWhere('type',5)->where('status',1)->orderBy('name')->get();
        $stores = Store::select('id', 'name')->where('status',1)->orderBy('name')->get();
        return view('admin.order.store-report', compact('data','request','user','stores'));
    }
}

