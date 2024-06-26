<?php

namespace App\Http\Requests\UserRequests;

use App\Http\Requests\BaseRequest;

class UploadCoverImageRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image_url' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ];
    }
}
