<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print {{ $data['title'] }}</title>
</head>
<body class="px-5" style="font-size: 12pt; font-family:arial; font-weight:normal;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">{{ $data['title'] }}</h2>
            <h5 class="mb-1">Periode {{ date('d-m-Y', strtotime($data['start_date'])) }} s/d {{ date('d-m-Y', strtotime($data['end_date'])) }}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 d-flex justify-content-between">
            <div>Kode Barang</div>
            <b>{{ $data['barang']->code }}</b>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 d-flex justify-content-between">
            <div>Nama Barang</div>
            <b>{{ $data['barang']->name }}</b>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>Kode Anggota</th>
                        <th>Nama Anggota</th>
                        <th>Tanggal Transaksi</th>
                        <th>No Faktur</th>
                        <th>Qty (Kg)</th>
                        <th>Harga (Rp)</th>
                        <th>Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-right">
                        <th colspan="5" class="px-1">Saldo {{ date('d-m-Y', strtotime('-1 day', strtotime($data['start_date']))) }}</th>
                        <th class="px-1">{{ number_format($data['qty'], 2, ',', '.') }}</th>
                        <th class="px-1" colspan="2">{{ number_format($data['saldo'], 2, ',', '.') }}</th>
                    </tr>
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($data['data'] as $value)
                        @php
                            $i++;
                            $data['qty'] += $value->qty;
                            $data['saldo'] += $value->harga_total_satuan;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->sale->member->code }}</td>
                            <td class="px-1">{{ $value->sale->member->name }}</td>
                            <td class="px-1">{{ date('d-m-Y', strtotime($value->sale->tanggal_jual)) }}</td>
                            <td class="px-1">{{ $value->sale->no_faktur }}</td>
                            <td class="text-right px-1">{{ number_format($value->qty, 2, ',','.') }}</td>
                            <td class="text-right px-1">{{ number_format($value->harga_jual, 2, ',','.') }}</td>
                            <td class="text-right px-1">{{ number_format($value->harga_total_satuan, 2, ',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-right">
                        <th class="px-1" colspan="5">Jumlah : </th>
                        <th class="px-1">{{number_format($data['qty'], 2, ',','.')}}</th>
                        <th class="px-1" colspan="2">{{number_format($data['saldo'], 2, ',','.')}}</th>
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