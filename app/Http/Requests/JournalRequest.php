<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JournalRequest extends FormRequest
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
            'transaction_date' => 'required',
            'reference_number' => 'required',
            'name' => 'required',
            'type' => 'required',
            'top_account' => 'required',
            'top_type' => 'required',
            'top_amount' => 'required',
            'bottom_account' => 'required',
            'bottom_type' => 'required',
            'bottom_amount' => 'required',
        ];
    }
}