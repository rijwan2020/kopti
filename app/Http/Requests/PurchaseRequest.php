<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'tanggal_beli' => 'required',
            'note' => 'nullable',
            'suplier_id' => 'required',
            'ref_number' => 'required',
            'barang' => 'required',
            'total' => 'required',
            'diskon' => 'nullable',
            'total_bayar' => 'required',
            'account' => 'required',
            'warehouse_id' => 'required'
        ];
    }
}