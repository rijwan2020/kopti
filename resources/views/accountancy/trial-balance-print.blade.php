<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Neraca Saldo</title>
    {{-- <style type="text/css" media="print">
        @page { 
            size: landscape;
        }
    </style> --}}
</head>
<body class="px-5" style="font-size: 10pt">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Neraca Saldo</h2>
            <h5 class="mb-2"> Per {{ date('d-m-Y', strtotime($data['end_date'])) }}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @if ($data['view'] == 'all')
                <table border="1" width="100%">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center align-middle">#</th>
                            <th rowspan="2" class="align-middle px-1">Kode Akun</th>
                            <th rowspan="2" class="align-middle px-1">Nama Akun</th>
                            <th colspan="2" class="align-middle text-center px-1">Saldo Awal (Rp)</th>
                            <th colspan="2" class="align-middle text-center px-1">Mutasi (Rp)</th>
                            <th colspan="2" class="align-middle text-center px-1">Saldo Akhir (Rp)</th>
                        </tr>
                        <tr>
                            <th class="px-1 text-center">Debit</th>
                            <th class="px-1 text-center">Kredit</th>
                            <th class="px-1 text-center">Debit</th>
                            <th class="px-1 text-center">Kredit</th>
                            <th class="px-1 text-center">Debit</th>
                            <th class="px-1 text-center">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = $total_debit = $total_kredit = $total_saldo_awal_debit = $total_saldo_awal_kredit = $total_saldo_penyesuaian_debit = $total_saldo_penyesuaian_kredit = 0;
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                                if ($value->type == 0) {
                                    if ($value->saldo_awal >= 0) {
                                        $saldo_awal_debit = $value->saldo_awal;
                                        $saldo_awal_kredit = 0;
                                    }else{
                                        $saldo_awal_kredit = $value->saldo_awal * -1;
                                        $saldo_awal_debit = 0;
                                    }

                                    if ($value->saldo_penyesuaian >= 0) {
                                        $saldo_penyesuaian_debit = $value->saldo_penyesuaian;
                                        $saldo_penyesuaian_kredit = 0;
                                    }else{
                                        $saldo_penyesuaian_kredit = $value->saldo_penyesuaian * -1;
                                        $saldo_penyesuaian_debit = 0;
                                    }
                                }else{
                                    if ($value->saldo_awal >= 0) {
                                        $saldo_awal_kredit = $value->saldo_awal;
                                        $saldo_awal_debit = 0;
                                    }else{
                                        $saldo_awal_kredit = 0;
                                        $saldo_awal_debit = $value->saldo_awal * -1;
                                    }

                                    if($value->saldo_penyesuaian >= 0){
                                        $saldo_penyesuaian_kredit = $value->saldo_penyesuaian;
                                        $saldo_penyesuaian_debit = 0;
                                    }else{
                                        $saldo_penyesuaian_kredit = 0;
                                        $saldo_penyesuaian_debit = $value->saldo_penyesuaian * -1;
                                    }
                                }
                                $total_saldo_awal_debit += $saldo_awal_debit;
                                $total_saldo_awal_kredit += $saldo_awal_kredit;
                                $total_debit += $value->debit;
                                $total_kredit += $value->kredit;
                                $total_saldo_penyesuaian_debit += $saldo_penyesuaian_debit;
                                $total_saldo_penyesuaian_kredit += $saldo_penyesuaian_kredit;
                            @endphp
                            <tr>
                                <td class="px-1 text-center">{{ $i }}</td>
                                <td class="px-1">{{ $value->code }}</td>
                                <td class="px-1">{{ $value->name }}</td>
                                <td class="text-right px-1">{{ number_format($saldo_awal_debit, 2, ',','.') }}</td>
                                <td class="text-right px-1">{{ number_format($saldo_awal_kredit, 2, ',','.') }}</td>
                                <td class="text-right px-1">{{ number_format($value->debit, 2, ',','.') }}</td>
                                <td class="text-right px-1">{{ number_format($value->kredit, 2, ',','.') }}</td>
                                <td class="text-right px-1">{{ number_format($saldo_penyesuaian_debit, 2, ',','.') }}</td>
                                <td class="text-right px-1">{{ number_format($saldo_penyesuaian_kredit, 2, ',','.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="text-right">
                            <th colspan="3" class="px-1">Jumlah :</th>
                            <th class="px-1">{{ number_format($total_saldo_awal_debit, 2, ',', '.') }}</th>
                            <th class="px-1">{{ number_format($total_saldo_awal_kredit, 2, ',', '.') }}</th>
                            <th class="px-1">{{ number_format($total_debit, 2, ',', '.') }}</th>
                            <th class="px-1">{{ number_format($total_kredit, 2, ',', '.') }}</th>
                            <th class="px-1">{{ number_format($total_saldo_penyesuaian_debit, 2, ',', '.') }}</th>
                            <th class="px-1">{{ number_format($total_saldo_penyesuaian_kredit, 2, ',', '.') }}</th>
                        </tr>
                    </tbody>
                </table>
            @else
                <table border="1" width="100%">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center align-middle">#</th>
                            <th rowspan="2" class="align-middle px-1">Kelompok Akun</th>
                            <th colspan="2" class="align-middle text-center px-1">Saldo Awal (Rp)</th>
                            <th colspan="2" class="align-middle text-center px-1">Mutasi (Rp)</th>
                            <th colspan="2" class="align-middle text-center px-1">Saldo Akhir (Rp)</th>
                        </tr>
                        <tr>
                            <th class="px-1 text-center">Debit</th>
                            <th class="px-1 text-center">Kredit</th>
                            <th class="px-1 text-center">Debit</th>
                            <th class="px-1 text-center">Kredit</th>
                            <th class="px-1 text-center">Debit</th>
                            <th class="px-1 text-center">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = $total_debit = $total_kredit = $total_saldo_awal_debit = $total_saldo_awal_kredit = $total_saldo_penyesuaian_debit = $total_saldo_penyesuaian_kredit = 0;
                        @endphp
                        @foreach ($data['group'] as $value)
                            @php
                                $i++;
                                if ($value->type == 0) {
                                    if ($value->saldo_awal >= 0) {
                                        $saldo_awal_debit = $value->saldo_awal;
                                        $saldo_awal_kredit = 0;
                                    }else{
                                        $saldo_awal_kredit = $value->saldo_awal * -1;
                                        $saldo_awal_debit = 0;
                                    }

                                    if ($value->saldo_penyesuaian >= 0) {
                                        $saldo_penyesuaian_debit = $value->saldo_penyesuaian;
                                        $saldo_penyesuaian_kredit = 0;
                                    }else{
                                        $saldo_penyesuaian_kredit = $value->saldo_penyesuaian * -1;
                                        $saldo_penyesuaian_debit = 0;
                                    }
                                }else{
                                    if ($value->saldo_awal >= 0) {
                                        $saldo_awal_kredit = $value->saldo_awal;
                                        $saldo_awal_debit = 0;
                                    }else{
                                        $saldo_awal_kredit = 0;
                                        $saldo_awal_debit = $value->saldo_awal * -1;
                                    }

                                    if($value->saldo_penyesuaian >= 0){
                                        $saldo_penyesuaian_kredit = $value->saldo_penyesuaian;
                                        $saldo_penyesuaian_debit = 0;
                                    }else{
                                        $saldo_penyesuaian_kredit = 0;
                                        $saldo_penyesuaian_debit = $value->saldo_penyesuaian * -1;
                                    }
                                }
                                $total_saldo_awal_debit += $saldo_awal_debit;
                                $total_saldo_awal_kredit += $saldo_awal_kredit;
                                $total_debit += $value->debit;
                                $total_kredit += $value->kredit;
                                $total_saldo_penyesuaian_debit += $saldo_penyesuaian_debit;
                                $total_saldo_penyesuaian_kredit += $saldo_penyesuaian_kredit;
                            @endphp
                            <tr>
                                <td class="px-1 text-center">{{ $i }}</td>
                                <td class="px-1">{{ $value->name }}</td>
                                <td class="text-right px-1">{{ number_format($saldo_awal_debit, 2, ',','.') }}</td>
                                <td class="text-right px-1">{{ number_format($saldo_awal_kredit, 2, ',','.') }}</td>
                                <td class="text-right px-1">{{ number_format($value->debit, 2, ',','.') }}</td>
                                <td class="text-right px-1">{{ number_format($value->kredit, 2, ',','.') }}</td>
                                <td class="text-right px-1">{{ number_format($saldo_penyesuaian_debit, 2, ',','.') }}</td>
                                <td class="text-right px-1">{{ number_format($saldo_penyesuaian_kredit, 2, ',','.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="text-right">
                            <th colspan="2" class="px-1">Jumlah :</th>
                            <th class="px-1">{{ number_format($total_saldo_awal_debit, 2, ',', '.') }}</th>
                            <th class="px-1">{{ number_format($total_saldo_awal_kredit, 2, ',', '.') }}</th>
                            <th class="px-1">{{ number_format($total_debit, 2, ',', '.') }}</th>
                            <th class="px-1">{{ number_format($total_kredit, 2, ',', '.') }}</th>
                            <th class="px-1">{{ number_format($total_saldo_penyesuaian_debit, 2, ',', '.') }}</th>
                            <th class="px-1">{{ number_format($total_saldo_penyesuaian_kredit, 2, ',', '.') }}</th>
                        </tr>
                    </tbody>
                </table>
            @endif
            
        </div>
    </div>
    <div class="row text-center mt-3">
        <div class="col-md-9">Mengetahui,</div>
        <div class="col-md-3">Kuningan, {{ date('d F Y', strtotime($data['end_date'])) }}</div>
        <div class="col-md-3">
            <div>Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['ketua'] ? $data['assignment']['ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-3">
            <div>Wakil Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['wakil_ketua'] ? $data['assignment']['wakil_ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-3">
            <div>Bendahara</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['bendahara'] ? $data['assignment']['bendahara'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-3">
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