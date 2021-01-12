<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
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
            'gender' => 'nullable',
            'place_of_birth' => 'nullable',
            'date_of_birth' => 'nullable',
            'religion' => 'nullable',
            'education' => 'nullable',
            'address' => 'nullable',
            'village_id' => 'nullable',
            'phone' => 'nullable',
            'region_id' => 'required',
            'craftman' => 'nullable',
            'soybean_ration' => 'nullable',
            'raw_material' => 'nullable',
            'adjuvant' => 'nullable',
            'extra_material' => 'nullable',
            'production_result' => 'nullable',
            'income' => 'nullable',
            'marketing' => 'nullable',
            'capital' => 'nullable',
            'experience' => 'nullable',
            'domicile' => 'nullable',
            'place_of_business' => 'nullable',
            'production_tool' => 'nullable',
            'criteria' => 'nullable',
            'ho_letter' => 'nullable|int',
            'license' => 'nullable|int',
            'imb_letter' => 'nullable|int',
            'pbb_letter' => 'nullable|int',
            'extinguisher' => 'nullable|int',
            'join_date' => 'nullable',
            'out_date' => 'nullable',
            'dependent' => 'nullable',
            'total_dependent' => 'nullable|int',
            'total_children' => 'nullable|int',
            'image' => 'nullable',
            'status' => 'nullable',
        ];
    }
}