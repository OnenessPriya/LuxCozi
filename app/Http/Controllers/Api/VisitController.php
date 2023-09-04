<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\UserArea;
use App\Models\User;
use App\Models\Visit;
use DB;
use Illuminate\Support\Facades\Validator;
class VisitController extends Controller
{
    // store visit start
	public function visitStart(Request $request)
	{
		$validator = Validator::make($request->all(), [
            "user_id" => "required",
            "area_id" => "required",
            "start_date" => "required",
            "start_time" => "required",
            "start_location" => "nullable",
            "start_lat" => "nullable",
            "start_lon" => "nullable",
        ]);

        if (!$validator->fails()) {
            $data = [
                "user_id" => $request->user_id,
                "area_id" => $request->area_id,
                "start_date" => $request->start_date,
                "start_time" => $request->start_time,
                "start_location" => $request->start_location,
                "start_lat" => $request->start_lat,
                "start_lon" => $request->start_lon,
                "created_at" => date('Y-m-d H:i:s'),
            ];

            $resp = DB::table('visits')->insertGetId($data);

            return response()->json(['error' => false, 'resp' => 'Visit started', 'visit_id' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
	}

    // store visit end
	public function visitEnd(Request $request)
	{
		$validator = Validator::make($request->all(), [
            "visit_id" => "required",
            "end_date" => "required",
            "end_time" => "required",
            "end_location" => "nullable",
            "end_lat" => "nullable",
            "end_lon" => "nullable",
        ]);

        if (!$validator->fails()) {
            $data = [
                "visit_id" => $request->visit_id,
                "end_date" => $request->end_date,
                "end_time" => $request->end_time,
                "end_location" => $request->end_location,
                "end_lat" => $request->end_lat,
                "end_lon" => $request->end_lon,
                "updated_at" => date('Y-m-d H:i:s'),
            ];

            DB::table('visits')->where('id', $request->visit_id)->update($data);

            return response()->json(['error' => false, 'resp' => 'Visit ended', 'data' => $data]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
	}


    //check visit started or not

    public function checkVisit(Request $request,$id)
    {
        $data = (object)[];
		$data->visit=Visit::where('user_id',$id)->where('start_date',date('Y-m-d'))->where('visit_id',NULL)->orderby('id','desc')->first();
        $data->user=User::where('id',$id)->first();
        if (empty($data->visit)) {
                return response()->json(['error'=>true, 'resp'=>'Start Your Visit']);
            } else {
                return response()->json(['error'=>false, 'resp'=>'Visit already started','area'=>$data->visit->areas->name,'visit_id'=>$data->visit->id,'user'=>$data->user]);
            } 
		
	}

    //activity list
    public function activityList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "date" => "required",
        ]);
        if (!$validator->fails()) 
        {
         $data = (object)[];
         $user_id = $_GET['user_id'];
         $date = $_GET['date'];
		 $data->activity=Activity::where('user_id',$user_id)->whereDate('date',$date)->orderby('id','desc')->get();
        if (count($data->activity)==0) {
                return response()->json(['error'=>true, 'resp'=>'No data found']);
            } else {
                return response()->json(['error'=>false, 'resp'=>'Activity List','data'=>$data->activity]);
            } 
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
		
	}

    //activity create

    public function activityStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "date" => "required",
            "time" => "required",
            "type" => "required",
            "comment" => "nullable",
            "location" => "nullable",
            "lat" => "nullable",
        ]);

        if (!$validator->fails()) {
            $data = [
                "user_id" => $request->user_id,
                "date" => $request->date,
                "time" => $request->time,
                "type" => $request->type,
                "comment" => $request->comment,
                "location" => $request->location,
                "lat" => $request->lat,
                "lng" => $request->lng,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ];

            $resp = DB::table('activities')->insertGetId($data);
            if( $resp){
                return response()->json(['error' => false, 'resp' => 'Activity stored successfully', 'data' => $resp]);
            }else{
                return response()->json(['error'=>true, 'resp'=>'Something happend']);
            }
           
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }

     //area list
     public function areaList(Request $request,$id)
     {
        $data=UserArea::where('user_id',$id)->with('areas:id,name')->get();
        if (count($data)==0) {
                 return response()->json(['error'=>true, 'resp'=>'No data found']);
        } else {
                 return response()->json(['error'=>false, 'resp'=>'Area List','data'=>$data]);
        } 
         
     }
}
