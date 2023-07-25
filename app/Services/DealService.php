<?php

namespace App\Services;

use App\Helper\Helper;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Models\Deal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DealService extends BaseService
{
    public function all()
    {
        try {
            $get_deals = Deal::whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('artist_deals')
                    ->where('artist_deals.user_id', '=', Auth::id())
                    ->whereRaw('deals.id = artist_deals.deal_id');
            })
            ->with('DealServices')
            ->where('status','active')
            ->select(['id','name','discount_percentage','image_url'])
            ->get();
            return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_CREATED['outcomeCode'], $get_deals);
        } catch (\Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist " . __CLASS__ . " : " . __FUNCTION__, $error);
            return false;
        }
    }
    
    public function dealJoin($id)
    {
        try {

            $data=[
                "user_id"=> Auth::id(),
                "deal_id"=>$id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
									
            $artist_deal = DB::table('artist_deals')->insertGetId($data);

            return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_CREATED['outcomeCode'], $artist_deal);
        } catch (\Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Deal " . __CLASS__ . " : " . __FUNCTION__, $error);
            return false;
        }
    }
}