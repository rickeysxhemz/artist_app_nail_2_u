<?php

namespace App\Http\Requests\Services;

use App\Http\Requests\BaseRequest;

class AddServicesRequest extends BaseRequest
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
            'service_id' => 'required',
            'price' => 'required',
        ];
    }
}
