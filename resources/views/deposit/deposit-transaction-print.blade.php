<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Data Transaksi Simpanan</title>
</head>
<body class="px-5" style="font-size: 12pt; font-family:arial; font-weight:normal;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h3 class="mb-1">Data Transaksi Simpanan</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>No Ref</th>
                        <th>Tanggal Transaksi</th>
                        <th>Nama Anggota</th>
                        <th>Kode Anggota</th>
                        <th>Wilayah</th>
                        <th>Debit (Rp)</th>
                        <th>Kredit (Rp)</th>
                        <th>Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-right">
                        <th class="px-1" colspan="8">Saldo Awal</th>
                        <th class="px-1">{{ $data['saldo_awal'] >= 0 ? number_format($data['saldo_awal'], 2, ',', '.') : '('.number_format($data['saldo_awal'] * -1, 2, ',', '.').')' }}</th>
                    </tr>
                    @php
                        $i = 0;
                        $saldo = $data['saldo_awal'];
                        $total_debit = $total_kredit = 0;
                    @endphp
                    @foreach ($data['data'] as $value)
                        @php
                            $i++;
                            $saldo += $value->kredit - $value->debit;
                            $total_debit += $value->debit;
                            $total_kredit += $value->kredit;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->reference_number }}</td>
                            <td class="px-1">{{ $value->transaction_date }}</td>
                            <td class="px-1">{{ $value->member->name }}</td>
                            <td class="px-1">{{ $value->member->code }}</td>
                            <td class="px-1">{{ $value->region->name }}</td>
                            <td class="px-1 text-right">{{ number_format($value->debit, 2, ',', '.') }}</td>
                            <td class="px-1 text-right">{{ number_format($value->kredit, 2, ',', '.') }}</td>
                            <td class="px-1 text-right">{{ $saldo >= 0 ? number_format($saldo, 2, ',', '.') : '('.number_format($saldo * -1, 2, ',', '.').')' }}</td>
                        </tr>
                    @endforeach
                    <tr class="text-right">
                        <th class="px-1" colspan="6">Saldo Akhir</th>
                        <th class="px-1 text-right">{{ number_format($total_debit, 2, ',', '.') }}</th>
                        <th class="px-1 text-right">{{ number_format($total_kredit, 2, ',', '.') }}</th>
                        <th class="px-1">{{ $saldo >= 0 ? number_format($saldo, 2, ',', '.') : '('.number_format($saldo * -1, 2, ',', '.').')' }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row text-center mt-3">
        <div class="col-md-6">Mengetahui,</div>
        <div class="col-md-6">Kuningan, {{ date('d F Y') }}</div>
        <div class="col-md-6">
            <div>Manager</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['manager'] ? $data['assignment']['manager'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-6">
            <div>Petugas</div>
            <div style="margin-top: 100px;">( {{ auth()->user()->name }} )</div>
        </div>
    </div>
    <script>
        window.print();
		setTimeout(window.close, 3000);
    </script>
</body>
</html>