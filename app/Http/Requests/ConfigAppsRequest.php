<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigAppsRequest extends FormRequest
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
            'besar_sp' => 'required',
            'besar_sw' => 'required',
            'next_code_anggota' => 'required',
            'next_code_non_anggota' => 'required',
            'journal_periode_start' => 'required',
            'journal_periode_end' => 'required',
            'akun_pembelian' => 'required',
            'akun_diskon_pembelian' => 'required',
            'akun_penjualan_anggota' => 'required',
            'akun_penjualan_non_anggota' => 'required',
            'akun_retur_penjualan_anggota' => 'required',
            'akun_retur_penjualan_non_anggota' => 'required',
            'piutang_penjualan_anggota' => 'required',
            'piutang_penjualan_non_anggota' => 'required',
            'rek_simpati_kopti1' => 'required',
            'rek_simpati_kopti2' => 'required',
            'rek_simpati_kopti3' => 'required',
            'akun_so_pusat' => 'required',
            'akun_so_gudang' => 'required',
            'akun_persediaan' => 'required',
            'akun_retur_pembelian' => 'required',
            'akun_susut_pembelian' => 'required',
        ];
    }
}