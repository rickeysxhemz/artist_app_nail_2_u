<?php

namespace App\Services;

use Exception;
use App\Helper\Helper;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RatingService extends BaseService
{
    public function getDetails()
    {
        try {
            $reviews = [];
            $rates = Rating::where('artist_id', Auth::id())->pluck('rating')->toArray();
            if (count($rates) != 0) {
                $overall_rating = array_sum($rates) / count($rates);
                $ratings = Rating::where('artist_id', Auth::id())->get();
                if ($ratings) {
                    foreach ($ratings as $rating) {

                        $user = User::find($rating['client_id']);
                        $temp['username'] = $user->username;
                        $temp['rating'] = $rating['rating'];
                        $temp['review'] = $rating['review'];
                        $temp['created_at'] = $rating['created_at'];
                        array_push($reviews, $temp);
                    }
                    return  [
                        'overall_rating' => round($overall_rating,2),
                        'ratings' => $reviews
                    ];
                }
            }
            return GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'];
        } catch (Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:RatingService: getDetails", $error);
            return false;
        }
    }
}
