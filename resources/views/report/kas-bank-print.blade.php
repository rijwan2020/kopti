<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Pemasukan/Pengeluaran Kas & Bank</title>
</head>
<body class="px-5" style="font-size: 12pt">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Laporan Pemasukan/Pengeluaran Kas & Bank</h2>
            <h5 class="mb-2">Tanggal {{ date('d-m-Y', strtotime($data['date'])) }}</h5>
        </div>
    </div>
    <table border="1" width="100%">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>Tanggal Transaksi</th>
                <th>No Ref</th>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Keterangan</th>
                <th>Debit (Rp)</th>
                <th>Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
            @endphp
            @foreach ($data['data'] as $value)
                @php
                    $i++;
                @endphp
                <tr>
                    <td class="text-center">{{$i}}</td>
                    <td class="px-1">{{ date('d M Y, H:i:s', strtotime($value->transaction_date)) }}</td>
                    <td class="px-1">{{ $value->account->code }}</td>
                    <td class="px-1">{{ $value->account->name }}</td>
                    <td class="px-1">{{ $value['reference_number'] }}</td>
                    <td class="px-1">{{ $value['name'] }}</td>
                    <td class="text-right px-1">{{ number_format($value->debit, 2, ',', '.') }}</td>
                    <td class="text-right px-1">{{ number_format($value->kredit, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">
        <div class="row text-right h5 mb-1">
            <div class="col-md-8">Saldo s/d {{ date('d-m-Y', strtotime('-1 day', strtotime($data['date']))) }} (Rp):</div>
            <div class="col-md-4">{{ $data['saldo_awal'] >= 0 ? number_format($data['saldo_awal'], 2, ',', '.'):'('.number_format($data['saldo_awal']*-1, 2, ',', '.').')' }}</div>
        </div>
        <div class="row text-right h5 mb-1">
            <div class="col-md-8">Total Mutasi Debit (Rp):</div>
            <div class="col-md-4">{{ number_format($data['total_debit'], 2, ',', '.') }}</div>
        </div>
        <div class="row text-right h5 mb-1">
            <div class="col-md-8">Total Mutasi Kredit (Rp):</div>
            <div class="col-md-4">{{ number_format($data['total_kredit'], 2, ',', '.') }}</div>
        </div>
        @php
            $total_saldo = $data['saldo_awal'] + $data['total_debit'] - $data['total_kredit'];
        @endphp
        <div class="row text-right h5 mb-3">
            <div class="col-md-8">Saldo s/d {{ date('d-m-Y', strtotime($data['date'])) }} (Rp):</div>
            <div class="col-md-4">{{ $total_saldo >= 0 ? number_format($total_saldo, 2, ',', '.'):'('.number_format($total_saldo*-1, 2, ',', '.').')' }}</div>
        </div>
    </div>
    <div class="row text-center mt-3">
        <div class="col-md-8">Mengetahui,</div>
        <div class="col-md-4">Kuningan, {{ date('d F Y', strtotime($data['date'])) }}</div>
        <div class="col-md-4">
            <div>Bendahara</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['bendahara'] ? $data['assignment']['bendahara'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-4">
            <div>Manager</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['manager'] ? $data['assignment']['manager'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-4">
            <div>Kasir</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['kasir'] ? $data['assignment']['kasir'] : '_____________________' }} )</div>
        </div>
    </div>
    <script>
        window.print();
		setTimeout(window.close, 3000);
    </script>
</body>
</html>