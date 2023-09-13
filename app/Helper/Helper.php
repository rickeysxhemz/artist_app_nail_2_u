<?php

namespace App\Helper;

use App\Models\ErrorLog;
use Carbon\Carbon;

class Helper
{
    public static function storeCvUrl($request)
    {
        if (!$request->hasFile('cv_url')) {
            return false;
        }

        $file = $request->File('cv_url');
        $file_name = $file->hashName();
        $file->storeAs('storage/artist/CVs', $file_name);
        $destination = 'storage/artist/CVs/' . $file_name;
        return $destination;
    }

    public static function storeImageUrl($request, $user)
    {
        if (!$request->hasFile('image_url')) {
            return false;
        }
        
        if ($user->image_url != 'storage/artist/images/default-profile-image.png') {
            unlink(base_path() . '/public/' . $user->image_url);
        }
        $file = $request->File('image_url');
        $file_name = $file->hashName();
        $request->image_url->move(public_path('storage/profileImages'), $file_name);
        $destination = 'storage/profileImages/' . $file_name;
        return $destination;
    }

    public static function storeSalonImage($request, $user)
    {
        if (!$request->hasFile('image_url')) {
            return false;
        }
        
        $file = $request->File('image_url');
        $file_name = $file->hashName();
        $request->image_url->move(public_path('storage/profileImages'), $file_name);
        $destination = 'storage/profileImages/' . $file_name;
        return $destination;
    }

    public static function storeServiceImage($request)
    {
        if (!$request->hasFile('service_img')) {
            return false;
        }
        
        $file = $request->File('service_img');
        $file_name = $file->hashName();
        $request->service_img->move(public_path('storage/ServiceImages'), $file_name);
        $destination = 'storage/ServiceImages/' . $file_name;
        return $destination;
    }

    public static function errorLogs($function_name, $error)
    {
        $error_log = new ErrorLog;
        $error_log->function_name = $function_name;
        $error_log->exception = $error;
        $error_log->save();
    }

    public static function returnRecord($outCome = null, $record = null)
    {
        return ['outcomeCode' => intval($outCome), 'record' => $record];
    }

    public static function getDays($time)
    {
        $to = Carbon::createFromFormat('Y-m-d H:s:i', $time);
        $from = Carbon::createFromFormat('Y-m-d H:s:i', now());

        $diff_in_days = $to->diffInDays($from);
        if ($diff_in_days == 0) {
            return 'Today';
        } else {
            return $diff_in_days . ' days ago';
        }
    }
}
