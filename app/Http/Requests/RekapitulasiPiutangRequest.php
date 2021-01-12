<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RekapitulasiPiutangRequest extends FormRequest
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
            'trxdate' => 'required',
            'no_ref' => 'required',
            'note' => 'required',
            'tipe' => 'required',
            'total' => 'required',
            'member_id' => 'required',
        ];
    }
}