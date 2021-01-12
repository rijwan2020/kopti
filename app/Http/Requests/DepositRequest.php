<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
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
            'member_id' => 'required',
            'deposit_type_id' => 'required',
            'account_number' => 'required',
            'beginning_balance' => 'nullable',
            'registration_date' => 'nullable',
            'principal_balance' => 'nullable',
            'obligatory_balance' => 'nullable',
            'account' => 'nullable',
        ];
    }
}