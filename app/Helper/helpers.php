<?php

use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Store;
use App\Models\State;
use App\Models\Team;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\Activity;
use Carbon\Carbon;
$datetime = date('Y-m-d H:i:s');

if (!function_exists('in_array_r')) {

    function in_array_r($item , $array){
        return preg_match('/"'.preg_quote($item, '/').'"/i' , json_encode($array));
    }
}

if(!function_exists('sendNotification')) {
    function sendNotification($sender, $receiver, $type, $route, $title, $body='')
    {
        $noti = new Notification();
        $noti->sender_id = $sender;
        $noti->receiver_id = $receiver;
        $noti->type = $type;
        $noti->route = $route;
        $noti->title = $title;
        $noti->body = $body;
        $noti->read_flag = 0;
        $noti->save();
    }
}

if (!function_exists('slugGenerate')) {
    function slugGenerate($title, $table) {
        $slug = Str::slug($title, '-');
        $slugExistCount = DB::table($table)->where('name', $title)->count();
        if ($slugExistCount > 0) $slug = $slug . '-' . ($slugExistCount + 1);
        return $slug;
    }
}

if (!function_exists('imageUpload')) {
    function imageUpload($image, $folder = 'image') {
        $imageName = randomGenerator();
        $imageExtension = $image->getClientOriginalExtension();
        $uploadPath = 'uploads/'.$folder.'/';

        $image->move(public_path($uploadPath), $imageName.'.'.$imageExtension);
        $imagePath = $uploadPath.$imageName.'.'.$imageExtension;
        return $imagePath;
    }
}


if (!function_exists('orderProductsUpdatedMatrix')) {
    function orderProductsUpdatedMatrix($productsArr) {
        // dd($productsArr);
        if (count($productsArr) > 0) {
            $newProductArr = [];
            $childrenSizes = ['35', '40', '45', '50', '55', '60', '65', '70','75'];

            foreach($productsArr as $key => $product) {
                //dd($product);
                if (!in_array($product['size_id'], $childrenSizes)) {
                    $matchString = $product['product']['name'].'-'.$product['color']['name'];

                    if (!in_array_r($matchString, $newProductArr)) {
                        $newProductArr[] = [
                            'match_string' => $matchString,
                            'product_name' => $product['product']['name'],
                            'product_style_no' => $product['product']['style_no'],
                            'color' => $product['color']['name'],
                            '75' => ($product['size']['name'] == "75") ? $product['qty'] : 0,
                            '80' => ($product['size']['name'] == "80") ? $product['qty'] : 0,
                            '85' => ($product['size']['name'] == "85") ? $product['qty'] : 0,
                            '90' => ($product['size']['name'] == "90") ? $product['qty']: 0,
                            '95' => ($product['size']['name'] == "95") ? $product['qty'] : 0,
                            '100' => ($product['size']['name'] == "100") ? $product['qty'] : 0,
                            '105' => ($product['size']['name'] == "105") ? $product['qty'] : 0,
                            '110' => ($product['size']['name'] == "110") ? $product['qty'] : 0,
                            '115' => ($product['size']['name'] == "115") ? $product['qty'] : 0,
                            '120' => ($product['size']['name'] == "120") ? $product['qty'] : 0,
                            'total' => $product['qty'],
                        ];
                    } else {
                        $i = array_search($matchString, array_column($newProductArr, 'match_string'));
    
                        ($product['size']['name'] == "75") ? $newProductArr[$i]['75'] += $product['qty'] : $newProductArr[$i]['75'] += 0;
                        ($product['size']['name'] == "80") ? $newProductArr[$i]['80'] += $product['qty'] : $newProductArr[$i]['80'] += 0;
                        ($product['size']['name'] == "85") ? $newProductArr[$i]['85'] += $product['qty'] : $newProductArr[$i]['85'] += 0;
                        ($product['size']['name'] == "90") ? $newProductArr[$i]['90'] += $product['qty'] : $newProductArr[$i]['90'] += 0;
                        ($product['size']['name'] == "95") ? $newProductArr[$i]['95'] += $product['qty'] : $newProductArr[$i]['95'] += 0;
                        ($product['size']['name'] == "100") ? $newProductArr[$i]['100'] += $product['qty'] : $newProductArr[$i]['100'] += 0;
                        ($product['size']['name'] == "105") ? $newProductArr[$i]['105'] += $product['qty'] : $newProductArr[$i]['105'] += 0;
                        ($product['size']['name'] == "110") ? $newProductArr[$i]['110'] += $product['qty'] : $newProductArr[$i]['110'] += 0;
                        ($product['size']['name'] == "115") ? $newProductArr[$i]['115'] += $product['qty'] : $newProductArr[$i]['115'] += 0;
                        ($product['size']['name'] == "120") ? $newProductArr[$i]['120'] += $product['qty'] : $newProductArr[$i]['120'] += 0;
                        $newProductArr[$i]['total'] += $product['qty'];
                    }
                }
            }
        }

        return $newProductArr;
    }
}

if (!function_exists('orderProductsUpdatedMatrixChild')) {
    function orderProductsUpdatedMatrixChild($productsArr) {
         //dd($productsArr);
        if (count($productsArr) > 0) {
            $newProductArr = [];
            $childrenSizes = ['35', '40', '45', '50', '55', '60', '65', '70','75'];

            foreach($productsArr as $key => $product) {
                if (in_array($product['size_id'], $childrenSizes)) {
                    $matchString = $product['product']['name'].'-'.$product['color']['name'];

                    if (!in_array_r($matchString, $newProductArr)) {
                        $newProductArr[] = [
                            'match_string' => $matchString,
                            'product_name' => $product['product']['name'],
                            'product_style_no' => $product['product']['style_no'],
                            'color' => $product['color']['name'],
                            '35' => ($product['size']['name'] == "35") ? $product['qty']  : 0,
                            '40' => ($product['size']['name'] == "40") ? $product['qty']  : 0,
                            '45' => ($product['size']['name'] == "45") ? $product['qty']  : 0,
                            '50' => ($product['size']['name'] == "50") ? $product['qty']  : 0,
                            '55' => ($product['size']['name'] == "55") ? $product['qty']  : 0,
                            '60' => ($product['size']['name'] == "60") ? $product['qty']  : 0,
                            '65' => ($product['size']['name'] == "65") ? $product['qty']  : 0,
                            '70' => ($product['size']['name'] == "70") ? $product['qty']  : 0,
                            '75' => ($product['size']['name'] == "75") ? $product['qty']  : 0,
                            'total' => $product['qty'] ,
                        ];
                    } else {
                        $i = array_search($matchString, array_column($newProductArr, 'match_string'));
    
                        ($product['size']['name'] == "35") ? $newProductArr[$i]['35'] += $product['qty']  : $newProductArr[$i]['35'] += 0;
                        ($product['size']['name'] == "40") ? $newProductArr[$i]['40'] += $product['qty']  : $newProductArr[$i]['40'] += 0;
                        ($product['size']['name'] == "45") ? $newProductArr[$i]['45'] += $product['qty']  : $newProductArr[$i]['45'] += 0;
                        ($product['size']['name'] == "50") ? $newProductArr[$i]['50'] += $product['qty']  : $newProductArr[$i]['50'] += 0;
                        ($product['size']['name'] == "55") ? $newProductArr[$i]['55'] += $product['qty']  : $newProductArr[$i]['55'] += 0;
                        ($product['size']['name'] == "60") ? $newProductArr[$i]['60'] += $product['qty']  : $newProductArr[$i]['60'] += 0;
                        ($product['size']['name'] == "65") ? $newProductArr[$i]['65'] += $product['qty']  : $newProductArr[$i]['65'] += 0;
                        ($product['size']['name'] == "70") ? $newProductArr[$i]['70'] += $product['qty']  : $newProductArr[$i]['70'] += 0;
                        ($product['size']['name'] == "75") ? $newProductArr[$i]['75'] += $product['qty']  : $newProductArr[$i]['75'] += 0;
                      
                        $newProductArr[$i]['total'] += $product->qty;
                    }
                }
            }
        }

        return $newProductArr;
    }
}

if (!function_exists('generateOrderNumber')) {
    function generateOrderNumber(string $type, int $id) {
        if ($type == "secondary") {
            $shortOrderCode = "SC";
            $orderData = Order::select('sequence_no')->latest('id')->first();
             
            if (!empty($orderData)) {
                if (!empty($orderData->sequence_no)) {
                    $new_sequence_no = (int) $orderData->sequence_no + 1;
                } else {
                    $new_sequence_no = 1;
                }

                $ordNo = sprintf("%'.07d", $new_sequence_no);

                $store_id = $id;
                $storeData = Store::where('id', $store_id)->with('states:id,name','areas:id,name')->first();
               
                if (!empty($storeData)) {
                    $state = $storeData->states->name;
                    
                    if ($state != "UP CENTRAL" || $state != "UP East" || $state != "UP WEST") {
                        $stateCodeData = State::where('name', $state)->first();
                        $stateCode = $stateCodeData->code;
                    } else {
                        if ($state == "UP CENTRAL") $stateCode = "UPC";
                        elseif ($state == "UP East") $stateCode = "UPE";
                        elseif ($state == "UP WEST") $stateCode = "UPW";
                    }

                    $order_no = "Lux-".date('Y').'-'.$shortOrderCode.'-'.$stateCode.'-'.$ordNo;
                   
                    return [$order_no, $new_sequence_no];
                } else {
                    return false;
                }
            }
        } else {
            $shortOrderCode = "PR";
            
        }
    }
}

if (!function_exists('findManagerDetails')) {
    function findManagerDetails($userName, $userType ) {
        switch ($userType) {
            case 1:
                $namagerDetails = "";
                break;
            case 2:
                $query=Team::select('nsm_id')->where('zsm_id',$userName)->groupby('zsm_id')->with('nsm')->first();
               
                if ($query) {
                    $namagerDetails = "<span class='text-dark'>NSM:</span> ".$query->nsm->name?? '';
                } else {
                    $namagerDetails = "";
                }
                break;
            case 3:
                $query=Team::select('nsm_id','zsm_id')->where('rsm_id',$userName)->groupby('rsm_id')->with('nsm','zsm')->first();
                
                if ($query) {
                    $namagerDetails = "<span class='text-dark'>NSM:</span> ".$query->nsm->name." 
                    <br> 
                    <span class='text-dark'>ZSM:</span> ".$query->zsm->name;
                } else {
                    $namagerDetails = "";
                }
                break;
            case 4:
                $query=Team::select('nsm_id','zsm_id','rsm_id')->where('sm_id',$userName)->orderby('id','desc')->with('nsm','zsm','rsm')->first();
                 //dd($query);
                if ($query) {
                    $namagerDetails = "<span class='text-dark'>NSM:</span> ".$query->nsm->name." 
                    <br> 
                    <span class='text-dark'>ZSM:</span> ".$query->zsm->name." 
                    <br> 
                    <span class='text-dark'>RSM:</span> ".$query->rsm->name;
                } else {
                    $namagerDetails = "";
                }
                break;
                case 5:
                   $query=Team::select('nsm_id','zsm_id','rsm_id','sm_id')->where('asm_id',$userName)->orderby('id','desc')->with('nsm','zsm','rsm','sm')->first();
                    //dd($userName);
                    if ($query) {
                        $namagerDetails = "<span class='text-dark'>NSM:</span> ".$query->nsm->name." 
                        <br> 
                        <span class='text-dark'>ZSM:</span> ".$query->zsm->name." 
                        <br> 
                        <span class='text-dark'>RSM:</span> ".$query->rsm->name."
                        <br> 
                        <span class='text-dark'>SM:</span> ".$query->sm->name;
                    } else {
                        $namagerDetails = "";
                    }
                    break;
                case 6:
                        $query=Team::select('nsm_id','zsm_id','rsm_id','sm_id','asm_id')->where('ase_id',$userName)->orderby('id','desc')->with('nsm','zsm','rsm','sm','asm')->first();
                        
                        if ($query) {
                            $namagerDetails = "<span class='text-dark'>NSM:</span> ".$query->nsm->name." 
                            <br> 
                            <span class='text-dark'>ZSM:</span> ".$query->zsm->name ." 
                            <br> 
                            <span class='text-dark'>RSM:</span> ".$query->rsm->name."
                            <br> 
                            <span class='text-dark'>SM:</span> ".$query->sm->name."
                            <br> 
                            <span class='text-dark'>ASM:</span> ".$query->asm->name;
                        } else {
                            $namagerDetails = "";
                        }
                        break;
            default: 
                $namagerDetails = "";
                break;
        }

        return $namagerDetails;
    }
}


if (!function_exists('userTypeName')) {
    function userTypeName($userType ) {
        switch ($userType) {
            case 1: $userTypeDetail = "NSM";break;
            case 2: $userTypeDetail = "ZSM";break;
            case 3: $userTypeDetail = "RSM";break;
            case 4: $userTypeDetail = "SM";break;
            case 5: $userTypeDetail = "ASM";break;
            case 6: $userTypeDetail = "ASE";break;
            case 7: $userTypeDetail = "Distributor";break;
            case 8: $userTypeDetail = "Retailer";break;
            default: $userTypeDetail = "";break;
        }
        return $userTypeDetail;
    }
}
function dates_month($month, $year) {
    $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $month_names = array();
    $date_values = array();

    for ($i = 1; $i <= $num; $i++) {
        $mktime = mktime(0, 0, 0, $month, $i, $year);
        $date = date("d (D)", $mktime);
        $month_names[$i] = $date;
        $date_values[$i] = date("Y-m-d", $mktime);
    }
    
    return ['month_names'=>$month_names,'date_values'=>$date_values];
}

function getFirstLastDayMonth($yearmonthval){
    // $yearmonthval = "2023-02";
    // First day of the month.
    $firstday = date('Y-m-01', strtotime($yearmonthval));
    // Last day of the month.
    $lastday = date('Y-m-t', strtotime($yearmonthval));
    return array('firstday'=>$firstday,'lastday'=>$lastday);
}

function dates_attendance($id, $date) {
    $day = date('D', strtotime($date));
    
    $date_wise_attendance = array();
    $d=array();
    $users = array();
    $user = User::where('id', $id)->first();

    if($user->type==2 || $user->type==3){
        
           // $res=UserLogin::join('other_activities', 'other_activities.user_id', 'user_logins.user_id')->where('user_logins.user_id',$id)->whereRaw("DATE_FORMAT(user_logins.created_at,'%Y-%m-%d')",$date)->get();
            $res=DB::select("select * from user_logins where user_id='$id' and is_login=1 and created_at like '$date%'");
            if (!empty($res)) {
                $d['is_present'] = 'P';
            }else if($day=='Sun' && empty($res))
            {
                $d['is_present'] = 'W';
            }else if($date > date('Y-m-d')){
                $d['is_present'] = '-';
            }
            else{
                $d['is_present'] = 'A';
            }

            array_push($date_wise_attendance, $d);
        
    }else{
        
            $res2=DB::select("select * from activities where user_id='$id' and date='$date'");
            
            if (!empty($res2)) {
                $d['is_present']  = 'P';
            }else if($day=='Sun'&& empty($res2))
            {
                $d['is_present'] = 'W';
            }else if($date > date('Y-m-d')){
                $d['is_present'] = '-';
            }
            else{
                $d['is_present']  = 'A';
            }

            array_push($date_wise_attendance, $d);
        
    }

    $data['date_wise_attendance'] = $date_wise_attendance;

    array_push($users, $data);
    
    return [$users];
}

if (!function_exists('findTeamDetails')) {
    function findTeamDetails($userName, $userType ) {
        $namagerDetails = array();
        $team_wise_attendance =array();
        switch ($userType) {
            case 1:
                $namagerDetails[] = "";
                break;
            case 2:
                $query=Team::select('nsm_id')->where('zsm_id',$userName)->groupby('zsm_id')->with('nsm')->first();
               
                if ($query) {
                    $namagerDetails['nsm'] = $query->nsm->name?? '';
                    $namagerDetails['zsm'] = "";
                    $namagerDetails['rsm'] = "";
                    $namagerDetails['sm'] = "";
                    $namagerDetails['asm'] = "";
                } else {
                    $namagerDetails[] = "";
                }
                break;
            case 3:
                $query=Team::select('nsm_id','zsm_id')->where('rsm_id',$userName)->groupby('rsm_id')->with('nsm','zsm')->first();
                
                if ($query) {
                    $namagerDetails['nsm'] = $query->nsm->name?? '';
                    $namagerDetails['zsm'] = $query->zsm->name?? '';
                    $namagerDetails['rsm'] = "";
                    $namagerDetails['sm'] = "";
                    $namagerDetails['asm'] = "";
                } else {
                    $namagerDetails[] = "";
                }
                break;
            case 4:
                $query=Team::select('nsm_id','zsm_id','rsm_id')->where('sm_id',$userName)->orderby('id','desc')->with('nsm','zsm','rsm')->first();
                
                if ($query) {
                    $namagerDetails['nsm'] = $query->nsm->name?? '';
                    $namagerDetails['zsm'] = $query->zsm->name?? '';
                    $namagerDetails['rsm'] = $query->rsm->name?? '';
                    $namagerDetails['sm'] = "";
                    $namagerDetails['asm'] = "";
                } else {
                    $namagerDetails[] = "";
                }
                break;
                case 5:
                    $query=Team::select('nsm_id','zsm_id','rsm_id','sm_id')->where('asm_id',$userName)->orderby('id','desc')->with('nsm','zsm','rsm','sm')->first();
                    
                    if ($query) {
                        $namagerDetails['nsm'] = $query->nsm->name?? '';
                        $namagerDetails['zsm'] = $query->zsm->name?? '';
                        $namagerDetails['rsm'] = $query->rsm->name?? '';
                        $namagerDetails['sm'] = $query->sm->name?? '';
                        $namagerDetails['asm'] = "";
                    } else {
                        $namagerDetails[]= "";
                    }
                    break;
                case 6:
                        $query=Team::select('nsm_id','zsm_id','rsm_id','sm_id','asm_id')->where('ase_id',$userName)->orderby('id','desc')->with('nsm','zsm','rsm','sm','asm')->first();
                        
                        if ($query) {
                            $namagerDetails['nsm'] = $query->nsm->name ?? '';
                            $namagerDetails['zsm'] = $query->zsm->name?? '';
                            $namagerDetails['rsm'] = $query->rsm->name?? '';
                            $namagerDetails['sm'] = $query->sm->name?? '';
                            $namagerDetails['asm'] = $query->asm->name?? '';
                        } else {
                            $namagerDetails[] = "";
                        }
                        break;
            default: 
                $namagerDetails[] = "";
                break;
        }
        array_push($team_wise_attendance, $namagerDetails);
      
        return $team_wise_attendance;
    }
}

function daysCount($from, $to,$userId) {
    $days=array();
    $d=array();
    $to = \Carbon\Carbon::parse($to);
    $from = \Carbon\Carbon::parse($from);
   
        $years = $to->diffInYears($from);
        $months = $to->diffInMonths($from);
        $weeks = $to->diffInWeeks($from);
        $days = $to->diffInDays($from);
        $hours = $to->diffInHours($from);
        $minutes = $to->diffInMinutes($from);
        $seconds = $to->diffInSeconds($from);
        $d['total_days']  = $days;
        $res2=DB::select("select * from activities where user_id='$userId' and (DATE(date) BETWEEN '".$from."' AND '".$to."') GROUP BY date");
        $d['work_count'] = count($res2);
        $leave=DB::select("select * from other_activities where user_id='$userId' and (DATE(date) BETWEEN '".$from."' AND '".$to."') and type='leave' GROUP BY date");
        $d['leave_count'] = count($leave);
        $sundays = intval($days / 7) + ($from->format('N') + $days % 7 >= 7);
        $d['weekend_count'] = $sundays;
        $storeCount=DB::select("select * from stores where user_id='$userId' and (DATE(created_at) BETWEEN '".$from."' AND '".$to."') GROUP BY created_at");
        $d['store_count'] = count($storeCount);
        $orderCount =DB::select("SELECT  IFNULL(SUM(op.qty), 0) AS product_count FROM `order_products` op
        INNER JOIN orders o ON o.id = op.order_id
        WHERE o.user_id = ".$userId."
        AND (DATE(op.created_at) BETWEEN '".$from."' AND '".$to."')
        GROUP BY o.user_id
         ");
         $d['order_count'] = $orderCount[0]->product_count ?? '';
         $orderoncallCount =DB::select("SELECT  IFNULL(SUM(op.qty), 0) AS product_count FROM `order_products` op
        INNER JOIN orders o ON o.id = op.order_id
        WHERE o.user_id = ".$userId." AND o.order_type='order-on-call'
        AND (DATE(op.created_at) BETWEEN '".$from."' AND '".$to."')
        GROUP BY o.user_id
         ");
         $d['order_on_call_count'] = $orderoncallCount[0]->product_count ?? '';
        //dd($d);
    return $d;
}


