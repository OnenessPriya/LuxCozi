<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportContrller extends Controller
{
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
                $query->where('user_id', $rsm);
            });
            $query->when($zsm, function($query) use ($zsm) {
                $query->where('user_id', $zsm);
            });

            $data = $query->latest('id')->paginate(25);
           
        } else {
            $data = UserLogin::latest('id')->paginate(25);
        }
        $zsm=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();
        $ases = User::select('id', 'name')->where('type', 6)->orWhere('type', 5)->orderBy('name')->get();
        
    
        return view('admin.report.login-report',compact('data', 'ases','request','zsm'));
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
