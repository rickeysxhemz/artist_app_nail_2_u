<?php

namespace App\Services;

use App\Libs\Response\GlobalApiResponseCodeBook;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\PasswordReset;
use App\Helper\Helper;
use App\Jobs\SendEmailVerificationMail;
use App\Models\EmailVerify;
use App\Models\Setting;
use App\Models\User;
use App\Models\OTP;
use Carbon\Carbon;
use Exception;
use Twilio\Rest\Client;

class AuthService extends BaseService
{
    public function register($request)
    {
        try {
            DB::beginTransaction();
            
            $userexist = User::where('email', $request->email)->first();
            // dd($userexist);
            if($userexist &&  $userexist->phone_verified_at == null){
                $phoneexist = User::where('phone_no', $request->phone_no)->first();
                
                if($phoneexist &&  $phoneexist->phone_verified_at == null){
                    
                    $user = User::find($phoneexist->id);
                    $user->username = $request->username;
                    $user->email = $request->email;
                    $user->password = Hash::make($request->password);
                    $user->phone_no = $request->phone_no;
                    $user->image_url = 'storage/artist/images/default-profile-image.png';
                    $user->cover_image = 'storage/artist/images/default-cover-image.PNG';
                    $user->zipcode = '123456';
                    // $store_cv_url = Helper::storeCvUrl($request);
                    // if ($store_cv_url)
                    //     $user->cv_url = $store_cv_url;
                    $user->save();

                    $otp = new OTP();
                    $otp->user_id = $user->id;
                    $otp->otp_value = random_int(100000, 999999);
                    // $otp->otp_value = '123456';
                    $otp->save();
                    
                    $account_sid = 'AC60d20bdd51da17c92e5dd29c9f22e521';
                    $auth_token = 'bb3720d64d89358fe6915c168f5474d4';
                    $twilio_number = '+13158478569';
                    
                    $receiverNumber = $request->phone_no;
                    $message = 'This message from Nails2u here is your six digit otp  ' . $otp->otp_value;
                    $client = new Client($account_sid, $auth_token);
                    $client->messages->create($receiverNumber, [
                        'from' => $twilio_number, 
                        'body' => $message]);

                    DB::commit();
                    return $user;
                }
    
                $user = User::find($userexist->id);
                $user->username = $request->username;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->phone_no = $request->phone_no;
                $user->image_url = 'storage/artist/images/default-profile-image.png';
                $user->cover_image = 'storage/artist/images/default-cover-image.PNG';
                $user->zipcode = '123456';
                // $store_cv_url = Helper::storeCvUrl($request);
                // if ($store_cv_url)
                //     $user->cv_url = $store_cv_url;
                $user->save();

                $otp = new OTP();
                $otp->user_id = $user->id;
                $otp->otp_value = random_int(100000, 999999);
                // $otp->otp_value = '123456';
                $otp->save();
                
                $account_sid = 'AC60d20bdd51da17c92e5dd29c9f22e521';
                $auth_token = 'bb3720d64d89358fe6915c168f5474d4';
                $twilio_number = '+13158478569';
                
                $receiverNumber = $request->phone_no;
                $message = 'This message from Nails2u here is your six digit otp  ' . $otp->otp_value;
                $client = new Client($account_sid, $auth_token);
                $client->messages->create($receiverNumber, [
                    'from' => $twilio_number, 
                    'body' => $message]);

                DB::commit();
                return $user;
            }
            
            if($userexist &&  $userexist->phone_verified_at !== null){
                return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_ALREADY_EXISTS['outcomeCode'], ['The email has already been taken.']);
            }
            $phoneexist = User::where('phone_no', $request->phone_no)->first();
            if($phoneexist &&  $phoneexist->phone_verified_at !== null){
                return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_ALREADY_EXISTS['outcomeCode'], ['The Phone has already been taken.']);
            }
            
            
            $user = new User();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone_no = $request->phone_no;
            $user->zipcode = '10010';
            $user->image_url = 'storage/artist/images/default-profile-image.png';
            $user->cover_image = 'storage/artist/images/default-cover-image.PNG';
            // $store_cv_url = Helper::storeCvUrl($request);
            // if ($store_cv_url)
            //     $user->cv_url = $store_cv_url;
            $user->save();


            // $verify_email_token = Str::random(140);
            // $email_verify = new EmailVerify;
            // $email_verify->email = $request->email;
            // $email_verify->token = $verify_email_token;
            // $email_verify->save();

            $setting = new Setting();
            $setting->user_id = $user->id;
            $setting->private_account = 0;
            $setting->secure_payment = 1;
            $setting->sync_contact_no = 0;
            $setting->app_notification = 1;
            $setting->save();

            $artist_role = Role::findByName('artist');
            $artist_role->users()->attach($user->id);

            // $mail_data = [
            //     'email' => $request->email,
            //     'token' => $verify_email_token
            // ];
            // SendEmailVerificationMail::dispatch($mail_data);

            $otp = new OTP();
            $otp->user_id = $user->id;
            $otp->otp_value = random_int(100000, 999999);
            // $otp->otp_value = '123456';
            $otp->save();
            
            $account_sid = 'AC60d20bdd51da17c92e5dd29c9f22e521';
            $auth_token = 'bb3720d64d89358fe6915c168f5474d4';
            $twilio_number = '+13158478569';
            
            $receiverNumber = $request->phone_no;
            $message = 'This message from Nails2u here is your six digit otp  ' . $otp->otp_value;
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'from' => $twilio_number, 
                'body' => $message]);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:AuthService: register", $error);
            return false;
        }
    }

    public function registerSalon($request)
    {
        // try {
            DB::beginTransaction();
            
            $userexist = User::where('email', $request->email)->first();
            
            if($userexist &&  $userexist->phone_verified_at == null){
                $phoneexist = User::where('phone_no', $request->phone_no)->first();
                
                if($phoneexist &&  $phoneexist->phone_verified_at == null){
                    
                    $user = User::find($phoneexist->id);
                    $user->username = $request->salonname;
                    $user->email = $request->email;
                    $user->password = Hash::make($request->password);
                    $user->phone_no = $request->phone_no;
                    $user->address = $request->address;
                    $user->cover_image = 'storage/artist/images/default-cover-image.PNG';
                    $user->zipcode = '123456';
                    $store_image_url = Helper::storeSalonImage($request, $user);
                    if ($store_image_url)
                        $user->image_url = $store_image_url;
                    $user->save();

                    $otp = new OTP();
                    $otp->user_id = $user->id;
                    $otp->otp_value = random_int(100000, 999999);
                    // $otp->otp_value = '123456';
                    $otp->save();
                    
                    $account_sid = 'AC60d20bdd51da17c92e5dd29c9f22e521';
                    $auth_token = 'bb3720d64d89358fe6915c168f5474d4';
                    $twilio_number = '+13158478569';
                    
                    $receiverNumber = $request->phone_no;
                    $message = 'This message from Nails2u here is your six digit otp  ' . $otp->otp_value;
                    $client = new Client($account_sid, $auth_token);
                    $client->messages->create($receiverNumber, [
                        'from' => $twilio_number, 
                        'body' => $message]);

                    DB::commit();
                    return $user;
                }
    
                $user = User::find($userexist->id);
                $user->username = $request->salonname;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->phone_no = $request->phone_no;
                $user->address = $request->address;
                $user->cover_image = 'storage/artist/images/default-cover-image.PNG';
                $user->zipcode = '123456';
                $store_image_url = Helper::storeSalonImage($request, $user);
                if ($store_image_url)
                    $user->image_url = $store_image_url;
                $user->save();

                $otp = new OTP();
                $otp->user_id = $user->id;
                $otp->otp_value = random_int(100000, 999999);
                // $otp->otp_value = '123456';
                $otp->save();
                
                $account_sid = 'AC60d20bdd51da17c92e5dd29c9f22e521';
                $auth_token = 'bb3720d64d89358fe6915c168f5474d4';
                $twilio_number = '+13158478569';
                
                $receiverNumber = $request->phone_no;
                $message = 'This message from Nails2u here is your six digit otp  ' . $otp->otp_value;
                $client = new Client($account_sid, $auth_token);
                $client->messages->create($receiverNumber, [
                    'from' => $twilio_number, 
                    'body' => $message]);

                DB::commit();
                return $user;
            }
            
            if($userexist &&  $userexist->phone_verified_at !== null){
                return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_ALREADY_EXISTS['outcomeCode'], ['The email has already been taken.']);
            }
            $phoneexist = User::where('phone_no', $request->phone_no)->first();
            if($phoneexist &&  $phoneexist->phone_verified_at !== null){
                return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_ALREADY_EXISTS['outcomeCode'], ['The Phone has already been taken.']);
            }
            
            
            $user = new User();
            $user->username = $request->salonname;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone_no = $request->phone_no;
            $user->address = $request->address;
            $user->zipcode = '10010';
            $user->cover_image = 'storage/artist/images/default-cover-image.PNG';
            $store_image_url = Helper::storeSalonImage($request, $user);
            if ($store_image_url)
                $user->image_url = $store_image_url;
            $user->save();


            // $verify_email_token = Str::random(140);
            // $email_verify = new EmailVerify;
            // $email_verify->email = $request->email;
            // $email_verify->token = $verify_email_token;
            // $email_verify->save();

            $setting = new Setting();
            $setting->user_id = $user->id;
            $setting->private_account = 0;
            $setting->secure_payment = 1;
            $setting->sync_contact_no = 0;
            $setting->app_notification = 1;
            $setting->save();

            $artist_role = Role::findByName('artist');
            $artist_role->users()->attach($user->id);

            // $mail_data = [
            //     'email' => $request->email,
            //     'token' => $verify_email_token
            // ];
            // SendEmailVerificationMail::dispatch($mail_data);

            $otp = new OTP();
            $otp->user_id = $user->id;
            $otp->otp_value = random_int(100000, 999999);
            // $otp->otp_value = '123456';
            $otp->save();
            
            $account_sid = 'AC60d20bdd51da17c92e5dd29c9f22e521';
            $auth_token = 'bb3720d64d89358fe6915c168f5474d4';
            $twilio_number = '+13158478569';
            
            $receiverNumber = $request->phone_no;
            $message = 'This message from Nails2u here is your six digit otp  ' . $otp->otp_value;
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'from' => $twilio_number, 
                'body' => $message]);

            DB::commit();
            return $user;
        // } catch (Exception $e) {
        //     DB::rollBack();
        //     $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
        //     Helper::errorLogs("Artist:AuthService: register", $error);
        //     return false;
        // }
    }

    public function login($request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $user = User::whereHas('roles', function ($q) {
                $q->where('name', 'artist');
            })
                ->where('email', '=', $credentials['email'])
                ->with('setting')
                ->first();

                if (
                    Hash::check($credentials['password'], isset($user->password) ? $user->password : null)
                    &&
                    $token = $this->guard()->attempt($credentials)
                ) {
                    if($user->phone_verified_at !== null){
                        $roles = Auth::user()->roles->pluck('name');
                        $data = Auth::user()->toArray();
                        unset($data['roles']);

                        $data = [
                            'access_token' => $token,
                            'token_type' => 'bearer',
                            'expires_in' => $this->guard()->factory()->getTTL() * 60*60*7,
                            'user' => Auth::user()->only('id', 'username', 'email', 'phone_no', 'address', 'experience', 'cv_url', 'image_url', 'total_balance', 'absolute_cv_url', 'absolute_image_url'),
                            'roles' => $roles,
                            'settings' => Auth::user()->setting->only('user_id', 'private_account', 'secure_payment', 'sync_contact_no', 'app_notification', 'language')
                        ];
                        return Helper::returnRecord(GlobalApiResponseCodeBook::SUCCESS['outcomeCode'], $data);
                    }
                    return Helper::returnRecord(GlobalApiResponseCodeBook::INVALID_CREDENTIALS['outcomeCode'], []);
                }    
                return Helper::returnRecord(GlobalApiResponseCodeBook::INVALID_CREDENTIALS['outcomeCode'], []);
        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:AuthService: login", $error);
            return false;
        }
    }

    public function forgotPassword($request)
    {
        try {
            DB::beginTransaction();
            if($request->has('email') && isset($request->email))
            {
                $password_reset_token = Str::random(140);
                $password_reset = new PasswordReset();
                $password_reset->email = $request->email;
                $password_reset->token = $password_reset_token;
                $password_reset->save();

                $user = User::whereHas('roles', function ($q) {
                                $q->where('name', 'artist');
                            })
                            ->where('email', $request->email)
                            ->first();
                if($user) {
                    
                    $response = [
                        "message" => "last 4 digits",
                        "digit" => substr($user->phone_no,-4)
                    ];
                    DB::commit();
                    return Helper::returnRecord(GlobalApiResponseCodeBook::SUCCESS['outcomeCode'], $response);
                } else {
                    
                    return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'], ['invalid email!']);
                }

                
            }
            else
            {
                $user = User::whereHas('roles', function ($q) {
                                $q->where('name', 'artist');
                            })
                            ->where('phone_no', $request->phone_number)
                            ->first();
                
                if($user){
                    
                    $otp = new OTP();
                    $otp->user_id = $user->id;
                    $otp->otp_value = random_int(100000, 999999);
                    // $otp->otp_value = '123456';
                    $otp->save();
        
                    $account_sid = 'AC60d20bdd51da17c92e5dd29c9f22e521';
                    $auth_token = 'bb3720d64d89358fe6915c168f5474d4';
                    $twilio_number = '+13158478569';
                    
                    $receiverNumber = $request->phone_number;
                    $message = 'This message from Nails2u here is your six digit otp  ' . $otp->otp_value;
                    // dd($message);
                    $client = new Client($account_sid, $auth_token);
                    $client->messages->create($receiverNumber, [
                        'from' => $twilio_number, 
                        'body' => $message]);
    
                    $response = [
                        "message" => "six digit code send your number!",
                        "phone_number" => $request->phone_number
                    ];
                    DB::commit();
                    return Helper::returnRecord(GlobalApiResponseCodeBook::SUCCESS['outcomeCode'], $response);
                } else {
                    return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'], ['invalid number!']);    
                }

                
            }
            // return $response;
        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("AuthService: forgotPassword", $error);
            return false;
        }
    }

    public function verifyCode($request)
    {
        try {

            if($request->has('email') && isset($request->email))
            {
                $user = User::where('email', $request->email)->first();
            }
            else 
            {
                $user = User::where('phone_no', $request->phone_number)->first();
            }

            $otp = OTP::where('user_id', $user->id)->where('otp_value', $request->code)->first();
            if($otp && $request->has('register_otp') && isset($request->register_otp)){
                $user->phone_verified_at = now();
                $user->save();

                OTP::where('user_id', $user->id)->latest()->delete();
            }
            return $otp;
        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("AuthService: verifyCode", $error);
            return false;
        }
    }

    public function verifyPhone($request)
    {
        try {

            $user = User::where('phone_no', $request->phone_no)->first();
            
            if($user && $user->phone_verified_at !== null){
                $model_has_roles = DB::table('model_has_roles')
                ->where('model_id', $user->id)->first();
                
                if($model_has_roles && $model_has_roles->role_id == '2') {
                    return "The phone number has already been taken as user";
                } else {
                    return "The phone number has already been taken";
                }
            } else {
                return "";
            }

        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("AuthService: verifyPhone", $error);
            return false;
        }
    }

    public function emailExist($request)
    {
        try {

            $user = User::where('email', $request->email)->first();
            
            if($user && $user->phone_verified_at !== null){
                $model_has_roles = DB::table('model_has_roles')
                ->where('model_id', $user->id)->first();
                
                if($model_has_roles && $model_has_roles->role_id == '2') {
                    return "The email has already been taken as user";
                } else {
                    if($request->has('type') && isset($request->type)){
                        return "";
                    }
                    return "The email has already been taken";
                }
            } else {
                return "";
            }

        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("AuthService: emailExist", $error);
            return false;
        }
    }

    public function resetPassword($request)
    {
        try {
            DB::beginTransaction();
            if($request->has('email') && isset($request->email))
            {
                $user = User::where('email', $request->email)->first();
            }
            else
            {
                $user = User::where('phone_no', $request->phone_number)->first();
            }
            $record = OTP::where('user_id', $user->id)
                ->where('otp_value', $request->code)->latest()->first();
            if ($record) {
                // $user = User::where('email', $email)->first();
                $user->password = Hash::make($request->password);
                $user->save();

                OTP::where('user_id', $user->id)->latest()->delete();

                $response = [
                    'message' => 'Password has been resetted!',
                ];
                DB::commit();
                return $response;
            }
            return intval(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode']);
        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("AuthService: resetPassword", $error);
            return false;
        }
    }

    public function logout()
    {
        try {
            Auth::logout();
            return true;
        } catch (Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:AuthService: logout", $error);
            return false;
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }

    public function verifyEmail($token, $email)
    {
        try {
            DB::beginTransaction();
            $record = EmailVerify::where('token', $token)
                ->where('email', $email)->latest()->first();
            if ($record) {
                $user = User::where('email', $email)->first();
                $user->email_verified_at = now();
                $user->save();

                EmailVerify::where('email', $email)->delete();

                $response = [
                    'message' => 'Email has been verified!',
                ];
                DB::commit();
                return $response;
            }
            return intval(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode']);
        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("AuthService: verifyEmail", $error);
            return false;
        }
    }
}
