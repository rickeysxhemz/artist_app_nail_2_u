<?php

namespace App\Services;

use App\Helper\Helper;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Models\User;
use App\Models\Booking;
use App\Models\UserPostedService;
use App\Models\SchedulerBooking;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardService extends BaseService
{
    public function userData()
    {
        try {

            $data = User::all();

            foreach ($data as $user) {
                $user->reviews()->avg('rating');
            }

            $latest_jobs = Booking::with(
                                    'BookingService:id,name as service_name',
                                    'Client:id,username,phone_no,address,image_url',
                                    'ScheduleBooking'
                                    )
                                    // ->whereDate('created_at', Carbon::today())
                                    ->where('artist_id', Auth::id())
                                    ->where('status', 'new')
                                    ->get();
            // Auth::user()->jobs()->with('BookingService:id,name as service_name', 'Client:id,username,phone_no,address,image_url')->whereDate('created_at', Carbon::today())->where('status', 'new')->take(10)->get(['id', 'artist_id', 'client_id', 'started_at', 'ended_at', 'status'])->toArray();


            $job_posts = UserPostedService::select('id','user_id', 'date', 'time', 'price', 'location', 'created_at')
                        ->with([
                            'Client:id,username,address,cv_url,image_url',
                            'PostService:id,name'
                        ])
                        ->where('status', 'active')
                        ->orderby('id','desc')
                        ->get();

            $today = Booking::with(
                'BookingService:id,name as service_name',
                'Client:id,username,phone_no,address,image_url',
                'ScheduleBooking',
                'BookingLocation'
                )
                ->whereDate('created_at', Carbon::today())
                ->whereIn('status', ['new','in-process'])
                ->where('artist_id', Auth::id())
                ->orderBy('id', 'desc')
                ->get();

            $jobs_details = [
                // 'today' => Auth::user()->jobs()->with('BookingService:id,name as service_name,price', 'Client:id,username,phone_no,address,image_url')->whereDate('created_at', Carbon::today())->where('status', 'in-process')->take(10)->get(['id', 'artist_id', 'client_id', 'started_at', 'ended_at', 'status'])->toArray(),
                // 'today' => Auth::user()->jobs()->with('BookingService:id,name as service_name', 'Client:id,username,phone_no,address,image_url')->whereDate('created_at', Carbon::today())->whereIn('status', ['new','in-process'])->take(10)->get(['id', 'artist_id', 'client_id', 'started_at', 'ended_at', 'status'])->toArray(),
                'today' => $today,
                'jobs' =>  $job_posts,
                // 'job_history' => Auth::user()->jobs()->with('BookingService:id,name as service_name,price', 'Client:id,username,phone_no,address,image_url')->where('status', 'done')->orderBy('id', 'desc')->take(10)->get(['id', 'artist_id', 'client_id', 'started_at', 'ended_at', 'status'])->toArray()
                'job_history' => Auth::user()->jobs()->with('BookingService:id,name as service_name', 'Client:id,username,phone_no,address,image_url', 'ScheduleBooking')->where('status', 'done')->orderBy('id', 'desc')->take(10)->get(['id', 'artist_id', 'client_id', 'started_at', 'ended_at', 'total_price', 'status'])->toArray()
            ];

            $user = [
                'latest_jobs_notification' => $latest_jobs,
                'user_data' => Auth::user()->only(['id','username','experience','total_balance']),
                'rating_reviews' => Auth::user()->reviews()->avg('rating'),
                'jobs_done' => Auth::user()->jobs()->where('status', 'done')->count(),
                'jobs_details' => $jobs_details
            ];


            return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_CREATED['outcomeCode'], $user);
        } catch (\Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:DashboardService: userData", $error);
            return false;
        }
    }

    public function userJobsDetails()
    {
        try {
            $date1 = Carbon::today()->toDateString();
            $date2 = Carbon::today()->subDays(3)->toDateString();

            $latest_jobs = Auth::user()->jobs()->whereBetween('created_at', [$date2, $date1])->get();

            $user_data = User::find(Auth::id())
                ->first(['id', 'username', 'total_balance', 'experience', 'image_url']);

            $user = [
                'user_data' => $user_data,
                'rating_reviews' => Auth::user()->reviews()->avg('rating'),
                'jobs_done' => Auth::user()->jobs()->where('status', 'done')->count(),
                'latest_jobs' => $latest_jobs
            ];

            return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_CREATED['outcomeCode'], $user);
        } catch (\Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:DashboardService: userData", $error);
            return false;
        }
    }

    public function acceptJob($id)
    {
        try {
            $auth_job = UserPostedService::with('PostService')
                        ->where('id', $id)
                        ->where('status', 'active')
                        ->first();

            if ($auth_job) {
                DB::begintransaction();
                $auth_job->status = 'accepted';
                $auth_job->save();

                $booking = new Booking();
                $booking->artist_id = Auth::id();
                $booking->status = 'new';
                $booking->client_id = $auth_job->user_id; 
                $booking->started_at = '5';
                $booking->total_price = $auth_job->price;
                $booking->save();

                $transaction = Transaction::where('user_posted_service_id', $id)->first();
                $transaction->booking_id = $booking->id;
                $transaction->receiver_id = Auth::id();
                $transaction->save();

                $services = DB::table('post_services')
                ->where('user_posted_service_id', $id)
                ->pluck('service_id')->toArray();

                $booking->BookingService()->attach($services);
                
                $scheduler_booking = new SchedulerBooking();
                $scheduler_booking->scheduler_id = '5';
                $scheduler_booking->user_id = Auth::id();
                $scheduler_booking->booking_id = $booking->id;
                $scheduler_booking->status = 'book';
                $scheduler_booking->date = date("m-d-Y", strtotime($auth_job->date));
                $scheduler_booking->save();
                DB::commit();
                
                return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_UPDATED['outcomeCode'], $auth_job->toArray());
            }

            return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'], []);
        } catch (\Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:DashboardService: userData", $error);
            return false;
        }
    }
    
    public function deviceToken($request)
    {
        try {
            DB::beginTransaction();
            $update_token = User::where('id', Auth::id())->first();
            if ($update_token) {
                $update_token->device_token = $request->device_token;
                $update_token->save();
                DB::commit();
                return GlobalApiResponseCodeBook::RECORD_UPDATED['outcomeCode'];
            } 
            return GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'];
        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:UserService: devicetoken", $error);
            return false;
        }
    }    
}
