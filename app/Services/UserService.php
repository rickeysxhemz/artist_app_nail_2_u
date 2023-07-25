<?php

namespace App\Services;

use App\Helper\Helper;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Models\Rating;
use App\Models\User;
use App\Models\Booking;
use App\Models\BookingLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Http\Traits\CommonTrait;

class UserService extends BaseService
{
    use CommonTrait;
    public function getProfileDetails()
    {
        try {

        // $expert_in_id = DB::table('booking_services')
        //     ->select('service_id', DB::raw('count(*) as total'))
        //     ->whereIn('booking_id', Auth::user()->jobs()->pluck('id'))
        //     ->groupBy('service_id')
        //     ->pluck('total')->toArray();

        // $expert_in = Auth::user()->services()->where('id', max($expert_in_id))->pluck('name');

        return [
            'username' => Auth::user()->username,
            'absolute_image_url' => url(Auth::user()->image_url),
            'phone_no' => Auth::user()->phone_no,
            'email' => Auth::user()->email,
            'address' => Auth::user()->address,
            'street_name' => Auth::user()->street_name,
            'city' => Auth::user()->city,
            'state' => Auth::user()->state,
            'zipcode' => Auth::user()->zipcode,
            'additional_info_status' => Auth::user()->additional_info_status,
            'rating' => Rating::where('artist_id', Auth::id())->avg('rating'),
            // 'expert' => (isset($expert_in[0])) ? $expert_in[0] : 'Nail paint'
            'expert' => (isset($expert_in[0])) ? $expert_in[0] : 'Nail paint'
        ];

        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:UserService: getProfileDetails", $error);
            return false;
        }
    }

    public function editProfile($request)
    {
        // try {
            DB::begintransaction();

            $user = User::find(Auth::id());

            if (isset($request->username)) {
                $user->username = $request->username;
            }

            if (isset($request->phone_no)) {
                $user->phone_no = $request->phone_no;
            }

            // if (isset($request->email)) {
            //     $user->email = $request->email;
            // }

            if (isset($request->password)) {
                $user->password = Hash::make($request->password);
            }

            if (isset($request->address)) {
                $user->address = $request->address;
            }

            if (isset($request->image_url)) {
                $path = Helper::storeImageUrl($request, $user);
                $user->image_url = $path;
            }

            $user->save();
            DB::commit();
            return $user;
        // } catch (Exception $e) {
        //     $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
        //     Helper::errorLogs("Artist:UserService: editProfile", $error);
        //     return false;
        // }
    }

    public function saveAddress($request)
    {
        try {
            DB::beginTransaction();
            $addresses = [];
            $user = User::find(Auth::id());
            if ($user->address) {
                $addresses = unserialize($user->address);
                array_push($addresses, $request->address);
                $user->address = serialize($addresses);
                $user->save();
                DB::commit();
                return $user->address;
            } else {
                array_push($addresses, $request->address);
                $user->address = serialize($addresses);
                $user->save();
                DB::commit();
                return $user;
            }
        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:UserService: saveAddress", $error);
            return false;
        }
    }

    public function getAddresses()
    {
         $user = User::find(Auth::id());
        try {
            $user = User::find(Auth::id());
            if ($user->address) {
                return unserialize($user->address);
            } else {
                return GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'];
            }
        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            dd($error);
            Helper::errorLogs("Artist:UserService: getAddresses", $error);
            return false;
        }
    }
    
    public function uploadCoverImage($request)
    {
        try {
            $file = $request->File('image_url');
            $file_name = $file->hashName();
            $request->image_url->move(public_path('storage/cover'), $file_name);
            $destination = 'storage/cover/' . $file_name;

            $user = User::find(Auth::id());
            
            $user->cover_image = $destination;
            $user->save();
            return $user;
        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:userService: uploadImage", $error);
            return false;
        }
    }

    public function getCoverImages()
    {
        try {
            $images = User::where('id', Auth::id())->pluck('cover_image')->first();
            if (!empty($images)) {
                return $images;
            }
            return GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'];
        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:PortfolioService: getImages", $error);
            return false;
        }
    }
    
    public function delete()
    {
        try {
            DB::beginTransaction();
            $user = User::where('id', Auth::id())
                ->whereHas("roles", function ($q) {
                    $q->where("name", "artist");
                })->first();
            // $user->roles()->detach();
            $user->delete();
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("UserService: delete", $error);
            return Helper::returnRecord(false, []);
        }
    }
    
    public function locationStart($request)
    {
        try {
            $booking_location = BookingLocation::where('booking_id', $request->booking_id)->first();
            if ($booking_location) {
                
                if ($booking_location->status == 'standby') {
                    DB::beginTransaction();
                    $booking_location->artist_longitude = $request->artist_longitude;
                    $booking_location->artist_latitude = $request->artist_latitude;
                    $booking_location->status = 'start';
                    $booking_location->save();

                    $booking = Booking::find($request->booking_id);
                    $artist_name = User::find($booking->artist_id);
                    $user = 'user';
                    $title = 'Location Alert';
                    $body = $artist_name->username.' start travelling toword your location';
                    $booking_created = '4';
                    $this->notifications($booking->client_id, $title, $body, $booking_created,  $user);

                    $user_name = User::find($booking->client_id);
                    $artist = 'artist';
                    $title = 'Location Alert';
                    $body = 'Your location started toword '.$user_name->username;
                    $booking_created = '4';
                    $this->notifications($booking->artist_id, $title, $body, $booking_created,  $artist);
                    DB::commit();
                    return GlobalApiResponseCodeBook::RECORD_UPDATED['outcomeCode'];
                } elseif($booking_location->status == 'start') {
                    DB::beginTransaction();
                    
                    $booking_location->artist_longitude = $request->artist_longitude;
                    $booking_location->artist_latitude = $request->artist_latitude;
                    $booking_location->save();
                    
                    
                    $data = [
                        'booking_id' => $request->booking_id,
                        'artist_longitude' => $request->artist_longitude,
                        'artist_latitude' => $request->artist_latitude
                    ];
                    $this->pusher('location_update', 'location_update', $data);
                    DB::commit();
                    return GlobalApiResponseCodeBook::RECORD_UPDATED['outcomeCode'];
                }
            } 
            return GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'];
        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:UserService: locationstart", $error);
            return false;
        }
    }

    public function locationReached($id)
    {
        try {
            DB::beginTransaction();
            $booking_location = BookingLocation::where('booking_id', $id)->where('status', 'start')->first();
            if ($booking_location) {
                $booking_location->status = 'reached';
                $booking_location->save();
                
                $booking = Booking::find($id);
                $artist_name = User::find($booking->artist_id);
                $user = 'user';
                $title = 'Location Alert';
                $body = $artist_name->username.' reached your location';
                $booking_created = '5';
                $this->notifications($booking->client_id, $title, $body, $booking_created,  $user);

                $user_name = User::find($booking->client_id);
                $artist = 'artist';
                $title = 'Location Alert';
                $body = 'You have reached '.$user_name->username . " location";
                $booking_created = '5';
                $this->notifications($booking->artist_id, $title, $body, $booking_created,  $artist);
                
                DB::commit();
                return GlobalApiResponseCodeBook::RECORD_UPDATED['outcomeCode'];
            } 
            return GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'];
        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:UserService: locationreached", $error);
            return false;
        }
    }
    
    public function additionalInfo($request)
    {
        try {
            DB::begintransaction();

            $user = User::find(Auth::id());
            $user->street_name = $request->street_name;
            $user->state = $request->state;
            $user->city = $request->city;
            $user->zipcode = $request->zipcode;
            $user->additional_info_status = '1';
            $user->save();

            DB::commit();
            return $user;
        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:UserService: additionalInfo", $error);
            return false;
        }
    }
}
