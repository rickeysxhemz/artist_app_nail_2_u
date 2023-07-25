<?php

namespace App\Services;

use Exception;
use App\Helper\Helper;
use App\Models\Booking;
use App\Models\Portfolio;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Libs\Response\GlobalApiResponseCodeBook;

class PortfolioService extends BaseService
{
    public function getDetails()
    {
        try {
            $rates = [];
            $visits = [];
            $user = User::find(Auth::id());
            $ratings = Rating::where('artist_id', Auth::id())->get(['rating']);

            foreach ($ratings as $rating) {
                array_push($rates, $rating['rating']);
            }

            $bookings = Booking::where('artist_id', Auth::id())->get('status');
            foreach ($bookings as $booking) {
                array_push($visits, $booking['status']);
            }

            if (count($rates) == 0) {
                $rating = 0;
            } else {
                $rating = array_sum($rates) / count(array_filter($rates));
            }

            $data = [
                'username' => $user->username,
                'experience' => $user->experience,
                'rating' => round($rating, 2),
                'Jobs done' => count($visits)
            ];

            return $data;
        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:PortfolioService: getDetails", $error);
            return false;
        }
    }

    public function getImages()
    {
        try {
            $pics = [];
            $images = Portfolio::where('artist_id', Auth::id())->get()->toArray();
            if (!empty($images)) {
                foreach ($images as $image) {
                    $temp['id'] = $image['id'];
                    $temp['title'] = $image['title'];
                    $temp['image_url'] = $image['image_url'];
                    $temp['absolute_image_url'] = $image['absolute_image_url'];
                    array_push($pics, $temp);
                }
                return $pics;
            }
            return GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'];
        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:PortfolioService: getImages", $error);
            return false;
        }
    }

    public function uploadImage($request)
    {
        try {
            $file = $request->File('image_url');
            $file_name = $file->hashName();
            $request->image_url->move(public_path('storage/portfolio'), $file_name);
            $destination = 'storage/portfolio/' . $file_name;

            $portfolio = new Portfolio;
            $portfolio->artist_id = Auth::id();
            $portfolio->title = $request->title;
            $portfolio->image_url = $destination;
            $portfolio->save();
            return $portfolio;
        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:PortfolioService: uploadImage", $error);
            return false;
        }
    }
    
    public function edit($request)
    {
        try {

            $portfolio = Portfolio::find($request->portfolio_id);
            if($portfolio){

                unlink(base_path() . '/public/' . $portfolio->image_url);

                $file = $request->File('image_url');
                $file_name = $file->hashName();
                $request->image_url->move(public_path('storage/portfolio'), $file_name);
                $destination = 'storage/portfolio/' . $file_name;

                $portfolio->title = $request->title;
                $portfolio->image_url = $destination;
                $portfolio->save();

                return $portfolio;
            }
            return GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'];

        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:PortfolioService: editPortfolio", $error);
            return false;
        }
    }

    public function deleteImage($request)
    {
        try {
            $portfolio = Portfolio::where('id', $request->image_id)->where('artist_id', Auth::id())->delete();
            return $portfolio;
        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:PortfolioService: deleteImage", $error);
            return false;
        }
    }
}
