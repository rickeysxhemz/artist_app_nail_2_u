<?php

namespace App\Http\Requests\UserRequests;

use App\Http\Requests\BaseRequest;

class AdditionalInfoRequest extends BaseRequest
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
            'street_name' => 'bail|required|string',
            'city' => 'bail|required|string|max:255',
            'state' => 'bail|required|string|max:255',
            'zipcode' => 'bail|required',
        ];
    }
}
