<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Rekapitulasi Penjualan Wilayah</title>
</head>
<body class="px-5" style="font-size: 12pt; font-family:arial; font-weight:normal;">
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
                        <th>Wilayah</th>
                        <th>Kebutuhan/Bulan (Kg)</th>
                        <th>Total Penjualan (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = $total_kebutuhan = $total_penjualan = 0;
                    @endphp
                    @foreach ($data['data'] as $value)
                        @php
                            $i++;
                            $kebutuhan = $value->member->sum('soybean_ration');
                            $total_kebutuhan += $kebutuhan;
                            $penjualan = $value->penjualan->where('tanggal_jual', '>=', $data['start_date'].' 00:00:00')->where('tanggal_jual', '<=', $data['end_date'].' 23:59:59')->sum('total_belanja');
                            $total_penjualan += $penjualan;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->name }}</td>
                            <td class="text-right px-1">{{ number_format($kebutuhan, 2, ',','.') }}</td>
                            <td class="text-right px-1">{{ number_format($penjualan, 2, ',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-right">
                        <th class="px-1" colspan="2">Jumlah</th>
                        <th class="px-1">{{ number_format($total_kebutuhan, 2, ',','.') }}</th>
                        <th class="px-1">{{ number_format($total_penjualan, 2, ',','.') }}</th>
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