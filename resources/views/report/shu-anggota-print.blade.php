<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Perhitungan Hasil Usaha</title>
</head>
<body class="px-5" style="font-size: 12pt">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Data Sisa Hasil Usaha Anggota</h2>
        </div>
    </div>

    <table border="1" width="100%">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>Kode Anggota</th>
                <th>Nama Anggota</th>
                <th>Wilayah</th>
                <th>Status</th>
                <th>SHU Simpanan (Rp)</th>
                <th>SHU Toko (Rp)</th>
                <th>Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
                $status = [
                    0 => 'Non Anggota',
                    1 => 'Anggota Aktif',
                    2 => 'Anggota Keluar'
                ];
            @endphp
            @foreach ($data['data'] as $key => $value)
                @php
                    $i++;
                    $shu = $value->shu_simpanan + $value->shu_toko;
                @endphp
                <tr>
                    <td class="text-center">{{ $i }}</td>
                    <td class="px-1">{{ $value->code }}</td>
                    <td class="px-1">{{ $value->name }}</td>
                    <td class="px-1">{{ $value->region->name }}</td>
                    <td class="px-1">{{ $status[$value->status] }}</td>
                    <td class="px-1 text-right">{{ number_format($value->shu_simpanan, 2, ',', '.') }}</td>
                    <td class="px-1 text-right">{{ number_format($value->shu_toko, 2, ',', '.') }}</td>
                    <td class="px-1 text-right">{{ number_format($shu, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
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