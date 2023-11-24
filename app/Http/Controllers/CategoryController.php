<?php

namespace App\Http\Controllers;

use App\Libs\Response\GlobalApiResponse;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(CategoryService $CategoryService, GlobalApiResponse $GlobalApiResponse)
    {
        $this->category_service = $CategoryService;
        $this->global_api_response = $GlobalApiResponse;
    }

    public function all()
    {
        $category_all = $this->category_service->all();

        if (!$category_all)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Category did not displayed!", $category_all));

        return ($this->global_api_response->success(1, "Category displayed successfully!", $category_all['record']));

    }
}
