<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetRequest extends FormRequest
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
            'name' => 'required',
            'asset_category_id' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'purchase_date' => 'required',
            'item_value' => 'required',
            'note' => 'nullable',
        ];
    }
}