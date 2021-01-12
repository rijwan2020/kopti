<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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
            'no_faktur' => 'required',
            'member_id' => 'required',
            'tanggal_jual' => 'required',
            'note' => 'nullable',
            'ref_number' => 'required',
            'barang' => 'required',
            'total_belanja' => 'required',
            'potongan_simpati1' => 'nullable',
            'potongan_simpati2' => 'nullable',
            'potongan_simpati3' => 'nullable',
            'total_bayar' => 'required',
            'account' => 'required',
        ];
    }
}