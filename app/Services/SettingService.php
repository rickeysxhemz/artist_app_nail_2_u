<?php

namespace App\Services;

use App\Helper\Helper;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingService extends BaseService
{
    public function update($request)
    {
        try {

            $setting_update = Setting::where('user_id', Auth::id())->first();

            if ($setting_update) {

                if (isset($request['private_account']))
                    $setting_update->private_account = $request->private_account;

                if (isset($request->secure_payment))
                    $setting_update->secure_payment = $request->secure_payment;

                if (isset($request->sync_contact_no))
                    $setting_update->sync_contact_no = $request->sync_contact_no;

                if (isset($request->app_notification))
                    $setting_update->app_notification = $request->app_notification;

                if (!empty($request->language))
                    $setting_update->language = $request->language;

                $setting_update->save();

                return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_UPDATED['outcomeCode'], $setting_update->toArray());
            }

        } catch (\Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:SettingService: add", $error);
            return false;
        }
    }
    public function getSetting()
    {
        try {
            $get_setting = Setting::where('user_id', Auth::id())->first();
             
            return $get_setting;

        } catch (\Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("SettingService: getSetting", $error);
            return false;
        }
    }
    public function resetPassword($request)
    {
        try {

            $user = User::where('id', Auth::id())->first();
            
            if ($user && Hash::check($request->password, $user->password)) {
                return intval(GlobalApiResponseCodeBook::RECORD_ALREADY_EXISTS['outcomeCode']);
            }
            if ($user) {
    
                $user->password = Hash::make($request->password);
                $user->save();

                $response = [
                    'message' => 'Password has been resetted!',
                ];
                return $response;
            }
            return intval(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode']);
        } catch (\Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("SettingService: resetPassword", $error);
            return false;
        }
    }
}
