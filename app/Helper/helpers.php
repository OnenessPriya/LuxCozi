<?php

use App\Models\Notification;

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
                    $matchString = $product['product']['style_no'].'-'.$product['color']['name'];

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
                    $matchString = $product['product']['style_no'].'-'.$product['color']['name'];

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

                    $order_no = "ONN-".date('Y').'-'.$shortOrderCode.'-'.$stateCode.'-'.$ordNo;

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