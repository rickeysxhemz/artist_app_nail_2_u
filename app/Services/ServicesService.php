<?php

namespace App\Services;

use App\Helper\Helper;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Models\ArtistService;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServicesService extends BaseService
{
    public function allRaw($request)
    {
        try {
            $services_all = Service::whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('artist_services')
                    ->where('artist_services.artist_id', '=', Auth::id())
                    ->whereRaw('services.id = artist_services.service_id');
            })
            ->where('status', 'admin')
            ->where('approve', '1')
            ->where('category_id', $request->category_id)
            ->orderBy('id', 'desc')
            ->get(['id','name']);

            return Helper::returnRecord(GlobalApiResponseCodeBook::SUCCESS['outcomeCode'], $services_all->toArray());

        } catch (\Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs(__FUNCTION__ . ' : ' . __CLASS__, $error);
            return false;
        }
    }
    
    public function all($request)
    {
        
        
        try {
            $artist_servie_data = [];
            
            $artist_services = Service::whereExists(function ($query) {
                $query->select('price')
                    ->from('artist_services')
                    ->where('artist_services.artist_id', '=', Auth::id())
                    ->whereRaw('services.id = artist_services.service_id');
            })
            ->where('approve', '1')
            ->where('category_id', $request->category_id)
            ->select('id','name')
            ->get();
            if($artist_services){
                foreach ($artist_services as $key => $artist_service) {
                    $service_price = ArtistService::where('artist_id', Auth::id())->where('service_id', $artist_service->id)->first();
                    $data['id'] = $artist_service->id;
                    $data['name'] = $artist_service->name;
                    $data['price'] = $service_price->price;
                   
                    
                    array_push($artist_servie_data, $data);
                }
            }
            return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_CREATED['outcomeCode'], $artist_servie_data);

        } catch (\Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:ServicesService: add", $error);
            return false;
        }
    }

    // public function add($request)
    // {
    //     try {
    //         $services = new Service();
    //         $services->artist_id = Auth::id();
    //         $services->name = $request->name;
    //         $services->price = $request->price;
    //         $services->save();

    //         return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_CREATED['outcomeCode'], $services->toArray());

    //     } catch (\Exception $e) {

    //         $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
    //         Helper::errorLogs("Artist:ServicesService: add", $error);
    //         return false;
    //     }
    // }
    
    public function add($request)
    {
        try {

            if($request->hasFile('service_img') && isset($request->service_img))
            {

                $artist_service = new Service();
                $artist_service->name = $request->name;
                $artist_service->status = 'artist';
                $artist_service->amount = $request->price;
                $artist_service->approve = '0';
                $artist_service->category_id = $request->category_id;
                $store_service_image_url = Helper::storeServiceImage($request);
                if ($store_service_image_url)
                    $artist_service->image = 'https://artist.nail2u.net/'.$store_service_image_url;
                $artist_service->save();

                $services = new ArtistService();
                $services->artist_id = Auth::id();
                $services->user_id = Auth::id();
                $services->service_id = $artist_service->id;
                $services->price = $request->price;
                $services->approve = '0';
                $services->save();

            } else {
                $services = new ArtistService();
                $services->artist_id = Auth::id();
                $services->user_id = Auth::id();
                $services->service_id = $request->service_id;
                $services->price = $request->price;
                $services->approve = '1';
                $services->save();
            }

            return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_CREATED['outcomeCode'], $services->toArray());

        } catch (\Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:ServicesService: add", $error);
            return false;
        }
    }
    
    public function edit($request)
    {
        try {

            $update_services = ArtistService::
            where('artist_id', Auth::id())
                ->where('service_id', $request['services_id'])->first();

            if ($update_services) {
                $update_services->price = $request['price'];
                // $update_services->start_date = $request['start_date'];
                // $update_services->end_date = $request['end_date'];
                $update_services->save();

                return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_UPDATED, $update_services->toArray());
            }

            return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode']);

        } catch (\Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:ServicesService: add", $error);
            return false;
        }
    }
    
    public function delete($request)
    {
        try {

            $delte_services = ArtistService::
            where('artist_id', Auth::id())
                ->where('service_id', $request['services_id'])->delete();

            if ($delte_services) {
                return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_UPDATED, ['service delete']);
            }

            return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode']);

        } catch (\Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:ServicesService: delete", $error);
            return false;
        }
    }

    public function removeDiscount($id)
    {
        try {

            $update_services = Service::
            where('artist_id', Auth::id())
                ->where('id', $id)->first();

            if ($update_services) {
                $update_services->discount_percentage = null;
                $update_services->start_date = null;
                $update_services->end_date = null;

                return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_UPDATED, $update_services->toArray());
            }

            return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode']);

        } catch (\Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:ServicesService: add", $error);
            return false;
        }
    }
}
