<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Buku Besar Pembantu Piutang</title>
</head>
<body class="px-5" style="font-size: 12pt; font-family:arial; font-weight:normal;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Buku Besar Pembantu Piutang {{ $data['data']->status == 1 ? "Angota" : "Non Anggota" }}</h2>
            <h5 class="mb-1">Periode {{ date('d-m-Y', strtotime($data['start_date'])) }} s/d {{ date('d-m-Y', strtotime($data['end_date'])) }}</h5>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-12">Nama Anggota : {{ $data['data']->name }}</div>
        <div class="col-md-12">Wilayah : {{ $data['data']->region->name }}</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>Tanggal Transaksi</th>
                        <th>No Ref</th>
                        <th>Keterangan</th>
                        <th>Debit (Rp)</th>
                        <th>Kredit (Rp)</th>
                        <th>Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-right">
                        <th class="px-1" colspan="6">Saldo Awal</th>
                        <th class="px-1">{{ number_format($data['saldo_awal'], 2, ',', '.') }}</th>
                    </tr>
                    @php
                        $i = 0;
                        $saldo = $data['saldo_awal'];
                    @endphp
                    @foreach ($data['list'] as $value)
                        @php
                            $i++;
                            if ($value->tipe) {
                                $debit = $value->total;
                                $kredit = 0;
                            }else{
                                $debit = 0;
                                $kredit = $value->total;
                            }
                            $saldo += $kredit - $debit;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->trxdate }}</td>
                            <td class="px-1">{{ $value->no_ref }}</td>
                            <td class="px-1">{{ $value->note }}</td>
                            <td class="text-right px-1">{{ number_format($kredit, 2, ',','.') }}</td>
                            <td class="text-right px-1">{{ number_format($debit, 2, ',','.') }}</td>
                            <td class="text-right px-1">{{ number_format($saldo, 2, ',','.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="text-right">
                        <th class="px-1" colspan="6">Jumlah</th>
                        <th class="px-1">{{ number_format($saldo, 2, ',','.') }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row text-center mt-3">
        <div class="col-md-12 text-right">Kuningan, {{ date('d F Y', strtotime($data['end_date'])) }}</div>
        <div class="col-md-12">Mengetahui</div>
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