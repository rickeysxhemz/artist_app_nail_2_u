<?php

namespace App\Http\Requests\UserRequests;

use App\Http\Requests\BaseRequest;

class LocationStartRequest extends BaseRequest
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
            'artist_longitude' => 'required|max:255|min:8',
            'artist_latitude' => 'required|max:255|min:8'
        ];
    }
}
