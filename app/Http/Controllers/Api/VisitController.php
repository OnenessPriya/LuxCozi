<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\UserArea;
class VisitController extends Controller
{
    // store visit start
	public function visitStart(Request $request)
	{
		$validator = Validator::make($request->all(), [
            "user_id" => "required",
            "area" => "required",
            "start_date" => "required",
            "start_time" => "required",
            "start_location" => "nullable",
            "start_lat" => "nullable",
            "start_lon" => "nullable",
        ]);

        if (!$validator->fails()) {
            $data = [
                "user_id" => $request->user_id,
                "area" => $request->area,
                "start_date" => $request->start_date,
                "start_time" => $request->start_time,
                "start_location" => $request->start_location,
                "start_lat" => $request->start_lat,
                "start_lon" => $request->start_lon,
            ];

            $resp = DB::table('visits')->insertGetId($data);

            return response()->json(['error' => false, 'message' => 'Visit started', 'visit_id' => $resp]);
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
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
            ];

            DB::table('visits')->where('id', $request->visit_id)->update($data);

            return response()->json(['error' => false, 'message' => 'Visit ended', 'data' => $data]);
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
	}


    //check visit started or not

    public function checkVisit(Request $request,$id)
    {
        $data = (object)[];
		$data->area=DB::table('visits')->where('user_id',$id)->where('start_date',date('Y-m-d'))->where('visit_id',NULL)->orderby('id','desc')->first();
        $data->user=User::where('id',$id)->first();
        if (count($data->area)==0) {
                return response()->json(['error'=>true, 'resp'=>'Start Your Visit']);
            } else {
                return response()->json(['error'=>false, 'resp'=>'Visit already started','area'=>$data->area->area,'visit_id'=>$data->area->id,'data'=>$data->user]);
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
		 $data->activity=Activity::where('user_id',$id)->whereDate('created_at',$request->date)->orderby('id','desc')->get();
        if (count($data->activity)==0) {
                return response()->json(['error'=>true, 'resp'=>'No data found']);
            } else {
                return response()->json(['error'=>false, 'resp'=>'Activity List','data'=>$data->activity]);
            } 
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
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
            ];

            $resp = DB::table('activities')->insertGetId($data);
            if( $resp){
                return response()->json(['error' => false, 'resp' => 'Activity stored successfully', 'data' => $resp]);
            }else{
                return response()->json(['error'=>true, 'resp'=>'Something happend']);
            }
           
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }

     //area list
     public function areaList(Request $request,$id)
     {
         $data = (object)[];
         $data->area=UserArea::where('user_id',$id)->get();
         if (count($data->area)==0) {
                 return response()->json(['error'=>true, 'resp'=>'No data found']);
            } else {
                 return response()->json(['error'=>false, 'resp'=>'Activity List','data'=>$data->area]);
             } 
         
     }
}
