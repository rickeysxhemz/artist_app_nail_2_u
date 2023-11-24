<?php

namespace App\Services;

use App\Helper\Helper;
use App\Libs\Response\GlobalApiResponseCodeBook;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Category;

class CategoryService extends BaseService
{
    public function all()
    {
        
        try {
            
            $category = Category::all();
            
            return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_CREATED['outcomeCode'], $category);

        } catch (\Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:CategoryService: all", $error);
            return false;
        }
    }
}