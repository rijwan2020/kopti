<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositTypeRequest extends FormRequest
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
            'code' => 'required',
            'name' => 'required',
            'description' => 'nullable',
            'type' => 'required',
            'term_type' => 'nullable',
            'term' => 'nullable',
            'account' => 'nullable',
            'group_id' => 'nullable'
        ];
    }
}