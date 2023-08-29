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
