<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Neraca</title>
    <style type="text/css" media="print">
        @page { 
            size: landscape;
        }
    </style>
</head>
<body class="px-5">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-sm-12 text-center">
            <h2 class="mb-1">Neraca</h2>
            <h5 class="mb-2">Periode {{ date('d M Y', strtotime($data['end_date'])) }}</h5>
        </div>
    </div>
    <div class="row" style="border: 1px solid black">
        <div class="col-sm-6 px-0 mr-0" style="width: 50%">
            @if ($data['view'] == 'all')
                <table border="1" width="100%" style="font-size: 10pt;">
                    <tbody>
                        <tr>
                            <th class="text-center h3" colspan="5">Aktiva</th>
                        </tr>
                        <tr class="text-center">
                            <th width="5%">#</th>
                            <th width="20%">Kode Akun</th>
                            <th width="35%">Nama Akun</th>
                            <th width="20%">{{ date('d M Y', strtotime($data['end_date'])) }} (Rp)</th>
                            <th width="20%">31 Dec {{ date('Y', strtotime('-1 year', strtotime($data['start_date']) ) ) }} (Rp)</th>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr>
                            <th class="text-center">I</th>
                            <th class="px-1" colspan="4">Aktiva Lancar</th>
                        </tr>
                        @php
                            $i = $aktiva_lancar = $aktiva_lancar_lalu = 0;
                        @endphp
                        @foreach ($data['data'] as $key => $value)
                            @if ($value->code[1] == 1 && $value->code[4] == 1 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu!=0))
                                @php
                                    $i++;
                                    if ($value->type == 0) {
                                        $saldo = $value->saldo_penyesuaian;
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value->saldo_penyesuaian * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $aktiva_lancar += $saldo;
                                    $aktiva_lancar_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">1.{{ $i }}</td>
                                    <td class="px-1">{{ $value->code }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="3" class="px-1">Total Aktiva Lancar :</th>
                            <th class="px-1">{{ $aktiva_lancar >= 0 ? number_format($aktiva_lancar,2,',','.') : '('.number_format($aktiva_lancar*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $aktiva_lancar_lalu >= 0 ? number_format($aktiva_lancar_lalu,2,',','.') : '('.number_format($aktiva_lancar_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        <tr>
                            <th class="text-center">II</th>
                            <th class="px-1" colspan="4">Investasi</th>
                        </tr>
                        @php
                            $i = $investasi = $investasi_lalu = 0;
                        @endphp
                        @foreach ($data['data'] as $key => $value)
                            @if ($value->code[1] == 1 && $value->code[4] == 2 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu!=0))
                                @php
                                    $i++;
                                    if ($value->type == 0) {
                                        $saldo = $value->saldo_penyesuaian;
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value->saldo_penyesuaian * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $investasi += $saldo;
                                    $investasi_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">2.{{ $i }}</td>
                                    <td class="px-1">{{ $value->code }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="3" class="px-1">Total Investasi :</th>
                            <th class="px-1">{{ $investasi >= 0 ? number_format($investasi,2,',','.') : '('.number_format($investasi*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $investasi_lalu >= 0 ? number_format($investasi_lalu,2,',','.') : '('.number_format($investasi_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        <tr>
                            <th class="text-center">III</th>
                            <th class="px-1" colspan="4">Aktiva Tetap</th>
                        </tr>
                        @php
                            $i = $aktiva_tetap = $aktiva_tetap_lalu = 0;
                        @endphp
                        @foreach ($data['data'] as $key => $value)
                            @if ($value->code[1] == 1 && $value->code[4] == 3 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu!=0))
                                @php
                                    $i++;
                                    if ($value->type == 0) {
                                        $saldo = $value->saldo_penyesuaian;
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value->saldo_penyesuaian * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $aktiva_tetap += $saldo;
                                    $aktiva_tetap_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">3.{{ $i }}</td>
                                    <td class="px-1">{{ $value->code }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="3" class="px-1">Total Aktiva Tetap :</th>
                            <th class="px-1">{{ $aktiva_tetap >= 0 ? number_format($aktiva_tetap,2,',','.') : '('.number_format($aktiva_tetap*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $aktiva_tetap_lalu >= 0 ? number_format($aktiva_tetap_lalu,2,',','.') : '('.number_format($aktiva_tetap_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        @php
                            $total_aktiva = $aktiva_lancar + $investasi + $aktiva_tetap;
                            $total_aktiva_lalu = $aktiva_lancar_lalu + $investasi_lalu + $aktiva_tetap_lalu;
                        @endphp
                    </tbody>
                </table>
            @else 
                <table border="1" width="100%" style="font-size: 10pt;">
                    <tbody>
                        <tr>
                            <th class="text-center h3" colspan="4">Aktiva</th>
                        </tr>
                        <tr class="text-center">
                            <th width="10%">#</th>
                            <th width="40%">Kelompok Akun</th>
                            <th width="25%">{{ date('d M Y', strtotime($data['end_date'])) }} (Rp)</th>
                            <th width="25%">31 Dec {{ date('Y', strtotime('-1 year', strtotime($data['start_date']) ) ) }} (Rp)</th>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr>
                            <th class="text-center">I</th>
                            <th class="px-1" colspan="3">Aktiva Lancar</th>
                        </tr>
                        @php
                            $i = $aktiva_lancar = $aktiva_lancar_lalu = 0;
                        @endphp
                        @foreach ($data['group'] as $key => $value)
                            @if ($value->account_id == 6 && ($value->saldo!=0 || $value->saldo_tahun_lalu != 0))
                                @php
                                    $i++;
                                    if ($value->type == 0) {
                                        $saldo = $value->saldo;
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value->saldo * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $aktiva_lancar += $saldo;
                                    $aktiva_lancar_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">1.{{ $i }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="2" class="px-1">Total Aktiva Lancar :</th>
                            <th class="px-1">{{ $aktiva_lancar >= 0 ? number_format($aktiva_lancar,2,',','.') : '('.number_format($aktiva_lancar*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $aktiva_lancar_lalu >= 0 ? number_format($aktiva_lancar_lalu,2,',','.') : '('.number_format($aktiva_lancar_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        <tr>
                            <th class="text-center">II</th>
                            <th class="px-1" colspan="3">Investasi</th>
                        </tr>
                        @php
                            $i = $investasi = $investasi_lalu = 0;
                        @endphp
                        @foreach ($data['group'] as $key => $value)
                            @if ($value->account_id == 7 && ($value->saldo!=0 || $value->saldo_tahun_lalu != 0))
                                @php
                                    $i++;
                                    if ($value->type == 0) {
                                        $saldo = $value->saldo;
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value->saldo * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $investasi += $saldo;
                                    $investasi_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">2.{{ $i }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="2" class="px-1">Total Investasi :</th>
                            <th class="px-1">{{ $investasi >= 0 ? number_format($investasi,2,',','.') : '('.number_format($investasi*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $investasi_lalu >= 0 ? number_format($investasi_lalu,2,',','.') : '('.number_format($investasi_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        <tr>
                            <th class="text-center">III</th>
                            <th class="px-1" colspan="3">Aktiva Tetap</th>
                        </tr>
                        @php
                            $i = $aktiva_tetap = $aktiva_tetap_lalu = 0;
                        @endphp
                        @foreach ($data['group'] as $key => $value)
                            @if ($value->account_id == 8 && ($value->saldo!=0 || $value->saldo_tahun_lalu != 0))
                                @php
                                    $i++;
                                    if ($value->type == 0) {
                                        $saldo = $value->saldo;
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value->saldo * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $aktiva_tetap += $saldo;
                                    $aktiva_tetap_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">3.{{ $i }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="2" class="px-1">Total Aktiva Tetap :</th>
                            <th class="px-1">{{ $aktiva_tetap >= 0 ? number_format($aktiva_tetap,2,',','.') : '('.number_format($aktiva_tetap*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $aktiva_tetap_lalu >= 0 ? number_format($aktiva_tetap_lalu,2,',','.') : '('.number_format($aktiva_tetap_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        @php
                            $total_aktiva_lalu = $aktiva_lancar_lalu + $investasi_lalu + $aktiva_tetap_lalu;
                            $total_aktiva = $aktiva_lancar + $investasi + $aktiva_tetap;
                        @endphp
                    </tbody>
                </table>
            @endif
            
        </div>
        <div class="col-sm-6 px-0 ml-0">
            @if ($data['view'] == 'all')
                <table border="1" width="100%" style="font-size: 10pt;">
                    <tbody>
                        <tr>
                            <th class="text-center h3" colspan="5">Pasiva</th>
                        </tr>
                        <tr class="text-center">
                            <th width="5%">#</th>
                            <th width="20%">Kode Akun</th>
                            <th width="35%">Nama Akun</th>
                            <th width="20%">{{ date('d M Y', strtotime($data['end_date'])) }} (Rp)</th>
                            <th width="20%">31 Dec {{ date('Y', strtotime('-1 year', strtotime($data['start_date']) ) ) }} (Rp)</th>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr>
                            <th class="text-center">IV</th>
                            <th class="px-1" colspan="4">Kewajiban Jangka Pendek</th>
                        </tr>
                        @php
                            $i = $kewajiban_jk_pendek = $kewajiban_jk_pendek_lalu = 0;
                        @endphp
                        @foreach ($data['data'] as $key => $value)
                            @if ($value->code[1] == 2 && $value->code[4] == 1 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu != 0))
                                @php
                                    $i++;
                                    if ($value->type == 1) {
                                        $saldo = $value->saldo_penyesuaian;
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value->saldo_penyesuaian * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $kewajiban_jk_pendek += $saldo;
                                    $kewajiban_jk_pendek_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">4.{{ $i }}</td>
                                    <td class="px-1">{{ $value->code }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="3" class="px-1">Total Kewajiban Jangka Pendek :</th>
                            <th class="px-1">{{ $kewajiban_jk_pendek >= 0 ? number_format($kewajiban_jk_pendek,2,',','.') : '('.number_format($kewajiban_jk_pendek*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $kewajiban_jk_pendek_lalu >= 0 ? number_format($kewajiban_jk_pendek_lalu,2,',','.') : '('.number_format($kewajiban_jk_pendek_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        <tr>
                            <th class="text-center">V</th>
                            <th class="px-1" colspan="4">Kewajiban Jangka Panjang</th>
                        </tr>
                        @php
                            $i = $kewajiban_jk_panjang = $kewajiban_jk_panjang_lalu = 0;
                        @endphp
                        @foreach ($data['data'] as $key => $value)
                            @if ($value->code[1] == 2 && $value->code[4] == 2 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu != 0))
                                @php
                                    $i++;
                                    if ($value->type == 1) {
                                        $saldo = $value->saldo_penyesuaian;
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value->saldo_penyesuaian * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $kewajiban_jk_panjang += $saldo;
                                    $kewajiban_jk_panjang_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">5.{{ $i }}</td>
                                    <td class="px-1">{{ $value->code }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="3" class="px-1">Total Kewajiban Jangka Panjang :</th>
                            <th class="px-1">{{ $kewajiban_jk_panjang >= 0 ? number_format($kewajiban_jk_panjang,2,',','.') : '('.number_format($kewajiban_jk_panjang*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $kewajiban_jk_panjang_lalu >= 0 ? number_format($kewajiban_jk_panjang_lalu,2,',','.') : '('.number_format($kewajiban_jk_panjang_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        <tr>
                            <th class="text-center">VI</th>
                            <th class="px-1" colspan="4">Modal</th>
                        </tr>
                        @php
                            $i = $modal = $modal_lalu = 0;
                        @endphp
                        @foreach ($data['data'] as $key => $value)
                            @if ($value->code[1] == 3 && $value->code[4] != 4 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu != 0))
                                @php
                                    $i++;
                                    if ($value->type == 1) {
                                        $saldo = $value->saldo_penyesuaian;
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value->saldo_penyesuaian * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $modal += $saldo;
                                    $modal_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">6.{{ $i }}</td>
                                    <td class="px-1">{{ $value->code }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="3" class="px-1">Total Modal :</th>
                            <th class="px-1">{{ $modal >= 0 ? number_format($modal,2,',','.') : '('.number_format($modal*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $modal_lalu >= 0 ? number_format($modal_lalu,2,',','.') : '('.number_format($modal_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        <tr>
                            <th class="text-center">VII</th>
                            <th class="px-1" colspan="4">PHU</th>
                        </tr>
                        @php
                            $i = $phu = $phu_lalu = 0;
                        @endphp
                        @foreach ($data['data'] as $key => $value)
                            @if ($value->code[1] == 3 && $value->code[4] == 4 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu != 0))
                                @php
                                    $i++;
                                    if ($value->type == 1) {
                                        $saldo = $value->saldo_penyesuaian;
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value->saldo_penyesuaian * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $phu += $saldo;
                                    $phu_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">7.{{ $i }}</td>
                                    <td class="px-1">{{ $value->code }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="3" class="px-1">Total PHU :</th>
                            <th class="px-1">{{ $phu >= 0 ? number_format($phu,2,',','.') : '('.number_format($phu*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $phu_lalu >= 0 ? number_format($phu_lalu,2,',','.') : '('.number_format($phu_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        @php
                            $total_pasiva = $kewajiban_jk_pendek + $kewajiban_jk_panjang + $modal + $phu;
                            $total_pasiva_lalu = $kewajiban_jk_pendek_lalu + $kewajiban_jk_panjang_lalu + $modal_lalu + $phu_lalu;
                        @endphp
                    </tbody>
                </table>
            @else
                <table border="1" width="100%" style="font-size: 10pt;">
                    <tbody>
                        <tr>
                            <th class="text-center h3" colspan="4">Pasiva</th>
                        </tr>
                        <tr class="text-center">
                            <th width="10%">#</th>
                            <th width="40%">Kelompok Akun</th>
                            <th width="25%">{{ date('d M Y', strtotime($data['end_date'])) }} (Rp)</th>
                            <th width="25%">31 Dec {{ date('Y', strtotime('-1 year', strtotime($data['start_date']) ) ) }} (Rp)</th>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr>
                            <th class="text-center">IV</th>
                            <th class="px-1" colspan="3">Kewajiban Jangka Pendek</th>
                        </tr>
                        @php
                            $i = $kewajiban_jk_pendek = $kewajiban_jk_pendek_lalu = 0;
                        @endphp
                        @foreach ($data['group'] as $key => $value)
                            @if ($value->account_id == 9 && ($value['saldo']!=0 || $value->saldo_tahun_lalu))
                                @php
                                    $i++;
                                    if ($value->type == 1) {
                                        $saldo = $value['saldo'];
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value['saldo'] * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $kewajiban_jk_pendek += $saldo;
                                    $kewajiban_jk_pendek_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">4.{{ $i }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="2" class="px-1">Total Kewajiban Jangka Pendek :</th>
                            <th class="px-1">{{ $kewajiban_jk_pendek >= 0 ? number_format($kewajiban_jk_pendek,2,',','.') : '('.number_format($kewajiban_jk_pendek*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $kewajiban_jk_pendek_lalu >= 0 ? number_format($kewajiban_jk_pendek_lalu,2,',','.') : '('.number_format($kewajiban_jk_pendek_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        <tr>
                            <th class="text-center">V</th>
                            <th class="px-1" colspan="3">Kewajiban Jangka Panjang</th>
                        </tr>
                        @php
                            $i = $kewajiban_jk_panjang = $kewajiban_jk_panjang_lalu = 0;
                        @endphp
                        @foreach ($data['group'] as $key => $value)
                            @if ($value->account_id == 10 && ($value['saldo']!=0 || $value->saldo_tahun_lalu))
                                @php
                                    $i++;
                                    if ($value->type == 1) {
                                        $saldo = $value['saldo'];
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value['saldo'] * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $kewajiban_jk_panjang += $saldo;
                                    $kewajiban_jk_panjang_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">5.{{ $i }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="2" class="px-1">Total Kewajiban Jangka Panjang :</th>
                            <th class="px-1">{{ $kewajiban_jk_panjang >= 0 ? number_format($kewajiban_jk_panjang,2,',','.') : '('.number_format($kewajiban_jk_panjang*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $kewajiban_jk_panjang_lalu >= 0 ? number_format($kewajiban_jk_panjang_lalu,2,',','.') : '('.number_format($kewajiban_jk_panjang_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        <tr>
                            <th class="text-center">VI</th>
                            <th class="px-1" colspan="3">Modal</th>
                        </tr>
                        @php
                            $i = $modal = $modal_lalu = 0;
                        @endphp
                        @foreach ($data['group'] as $key => $value)
                            @if (in_array($value->account_id, [11,12,13]) && ($value['saldo']!=0 || $value->saldo_tahun_lalu))
                                @php
                                    $i++;
                                    if ($value->type == 1) {
                                        $saldo = $value['saldo'];
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value['saldo'] * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $modal += $saldo;
                                    $modal_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">6.{{ $i }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="2" class="px-1">Total Modal :</th>
                            <th class="px-1">{{ $modal >= 0 ? number_format($modal,2,',','.') : '('.number_format($modal*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $modal_lalu >= 0 ? number_format($modal_lalu,2,',','.') : '('.number_format($modal_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        <tr>
                            <th class="text-center">VII</th>
                            <th class="px-1" colspan="3">PHU</th>
                        </tr>
                        @php
                            $i = $phu = $phu_lalu = 0;
                        @endphp
                        @foreach ($data['group'] as $key => $value)
                            @if ($value->account_id == 14 && ($value['saldo']!=0 || $value->saldo_tahun_lalu))
                                @php
                                    $i++;
                                    if ($value->type == 1) {
                                        $saldo = $value['saldo'];
                                        $saldo_lalu = $value->saldo_tahun_lalu;
                                    }else{
                                        $saldo = $value['saldo'] * -1;
                                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                    }
                                    $phu += $saldo;
                                    $phu_lalu += $saldo_lalu;
                                @endphp
                                <tr>
                                    <td class="px-1 text-center">7.{{ $i }}</td>
                                    <td class="px-1">{{ $value->name }}</td>
                                    <td class="text-right px-1">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                    <td class="text-right px-1">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="text-right">
                            <th colspan="2" class="px-1">Total PHU :</th>
                            <th class="px-1">{{ $phu >= 0 ? number_format($phu,2,',','.') : '('.number_format($phu*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $phu_lalu >= 0 ? number_format($phu_lalu,2,',','.') : '('.number_format($phu_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                        @php
                            $total_pasiva = $kewajiban_jk_pendek + $kewajiban_jk_panjang + $modal + $phu;
                            $total_pasiva_lalu = $kewajiban_jk_pendek_lalu + $kewajiban_jk_panjang_lalu + $modal_lalu + $phu_lalu;
                        @endphp
                        <tr class="text-right">
                            <th colspan="2" class="px-1">Total Pasiva :</th>
                            <th class="px-1">{{ $total_pasiva >= 0 ? number_format($total_pasiva,2,',','.') : '('.number_format($total_pasiva*-1,2,',','.').')' }}</th>
                            <th class="px-1">{{ $total_pasiva_lalu >= 0 ? number_format($total_pasiva_lalu,2,',','.') : '('.number_format($total_pasiva_lalu*-1,2,',','.').')' }}</th>
                        </tr>
                    </tbody>
                </table>
            @endif
            
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 px-0 mr-0" style="width: 50%">
            @if ($data['view'] == 'all')
                <table border="1" width="100%" style="font-size: 10pt;">
                    <tr class="text-right">
                        <th class="px-1" width="60%" colspan="3">Total Aktiva :</th>
                        <th class="px-1" width="20%">{{ $total_aktiva >= 0 ? number_format($total_aktiva,2,',','.') : '('.number_format($total_aktiva*-1,2,',','.').')' }}</th>
                        <th class="px-1" width="20%">{{ $total_aktiva_lalu >= 0 ? number_format($total_aktiva_lalu,2,',','.') : '('.number_format($total_aktiva_lalu*-1,2,',','.').')' }}</th>
                    </tr>
                </table>
            @else
                <table border="1" width="100%" style="font-size: 10pt;">
                    <tr class="text-right">
                        <th class="px-1" width="50%" colspan="3">Total Aktiva :</th>
                        <th class="px-1" width="25%">{{ $total_aktiva >= 0 ? number_format($total_aktiva,2,',','.') : '('.number_format($total_aktiva*-1,2,',','.').')' }}</th>
                        <th class="px-1" width="25%">{{ $total_aktiva_lalu >= 0 ? number_format($total_aktiva_lalu,2,',','.') : '('.number_format($total_aktiva_lalu*-1,2,',','.').')' }}</th>
                    </tr>
                </table>
            @endif
        </div>
        <div class="col-sm-6 px-0 mr-0" style="width: 50%">
            @if ($data['view'] == 'all')
                <table border="1" width="100%" style="font-size: 10pt;">
                    <tr class="text-right">
                        <th class="px-1" width="60%" colspan="3">Total Pasiva :</th>
                        <th class="px-1" width="20%">{{ $total_pasiva >= 0 ? number_format($total_pasiva,2,',','.') : '('.number_format($total_pasiva*-1,2,',','.').')' }}</th>
                        <th class="px-1" width="20%">{{ $total_pasiva_lalu >= 0 ? number_format($total_pasiva_lalu,2,',','.') : '('.number_format($total_pasiva_lalu*-1,2,',','.').')' }}</th>
                    </tr>
                </table>
            @else
                <table border="1" width="100%" style="font-size: 10pt;">
                    <tr class="text-right">
                        <th class="px-1" width="50%" colspan="3">Total Pasiva :</th>
                        <th class="px-1" width="25%">{{ $total_pasiva >= 0 ? number_format($total_pasiva,2,',','.') : '('.number_format($total_pasiva*-1,2,',','.').')' }}</th>
                        <th class="px-1" width="25%">{{ $total_pasiva_lalu >= 0 ? number_format($total_pasiva_lalu,2,',','.') : '('.number_format($total_pasiva_lalu*-1,2,',','.').')' }}</th>
                    </tr>
                </table>
            @endif
        </div>
    </div>

    <div class="row text-center mt-3">
        <div class="col-sm-9">Mengetahui,</div>
        <div class="col-sm-3">Kuningan, {{ date('d F Y', strtotime($data['end_date'])) }}</div>
        <div class="col-sm-3">
            <div>Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['ketua'] ? $data['assignment']['ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-sm-3">
            <div>Wakil Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['wakil_ketua'] ? $data['assignment']['wakil_ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-sm-3">
            <div>Bendahara</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['bendahara'] ? $data['assignment']['bendahara'] : '_____________________' }} )</div>
        </div>
        <div class="col-sm-3">
            <div>Manager</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['manager'] ? $data['assignment']['manager'] : '_____________________' }} )</div>
        </div>
    </div>
    <script>
        window.print();
		setTimeout(window.close, 3000);
    </script>
</body>
</html>