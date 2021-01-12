<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositTransactionRequest extends FormRequest
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
            'deposit_id' => 'required',
            'transaction_date' => 'required',
            'type' => 'required',
            'debit' => 'nullable',
            'kredit' => 'nullable',
            'reference_number' => 'required',
            'account' => 'required',
            'note' => 'nullable',
            'month' => 'nullable'
        ];
    }
}