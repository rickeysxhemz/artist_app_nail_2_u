<?php

namespace App\Http\Requests\Accounts;

use App\Http\Requests\BaseRequest;

class AccountLinkRequest extends BaseRequest
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
            'account_type' => 'required',
            'routing_number' => 'required',
            'account_number' => 'required'
        ];
    }
}
