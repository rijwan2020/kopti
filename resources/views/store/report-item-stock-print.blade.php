<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Laporan Persediaan</title>
</head>
<body class="px-5"  style="font-size: 12pt; font-family:arial; font-weight:normal;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Laporan Persediaan Barang {{ $data['gudang']->name ?? 'Pusat' }}</h2>
            <h5 class="mb-1">Periode {{ date('d-m-Y', strtotime($data['start_date'])) }} s/d {{ date('d-m-Y', strtotime($data['end_date'])) }}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Stok s/d {{ date('d-m-Y', strtotime('-1 day', strtotime($data['start_date']))) }} (Kg)</th>
                        <th>Masuk (Kg)</th>
                        <th>Keluar (Kg)</th>
                        <th>Stok Akhir (Kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($data['data'] as $value)
                        @php
                            $i++;
                            $saldo = $value->saldo_awal + $value->masuk - $value->keluar;
                        @endphp
                        <tr>
                            <td class="px-1">{{ $i }}</td>
                            <td class="px-1">{{ $value->code }}</td>
                            <td class="px-1">{{ $value->name }}</td>
                            <td class="text-right px-1">{{ number_format($value->saldo_awal, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($value->masuk, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($value->keluar, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($saldo, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-right">
                        <th class="px-1" colspan="3">Jumlah : </th>
                        <th class="px-1">{{ $data['total_saldo_awal'] >= 0 ? number_format($data['total_saldo_awal'], 2, ',', '.') : '('.number_format($data['total_saldo_awal']*-1, 2, ',', '.').')' }}</th>
                        <th class="px-1">{{ number_format($data['total_masuk'], 2, ',', '.') }}</th>
                        <th class="px-1">{{ number_format($data['total_keluar'], 2, ',', '.') }}</th>
                        <th class="px-1">{{ $data['total_persediaan'] >= 0 ? number_format($data['total_persediaan'], 2, ',', '.') : '('.number_format($data['total_persediaan']*-1, 2, ',', '.').')' }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="row text-center mt-3">
        <div class="col-md-8">Mengetahui,</div>
        <div class="col-md-4">Kuningan, {{ date('d F Y', strtotime($data['end_date'])) }}</div>
        <div class="col-md-4">
            <div>Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['ketua'] ? $data['assignment']['ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-4">
            <div>Bendahara</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['bendahara'] ? $data['assignment']['bendahara'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-4">
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