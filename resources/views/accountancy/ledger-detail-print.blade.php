<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Buku Besar</title>
</head>
<body class="px-5" style="font-size: 12pt">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Buku Besar</h2>
            <h4 class="mb-1">[{{ $data['account']->code }}] - {{ $data['account']->name }}</h4>
            <h5 class="mb-2"> {{date('d M Y', strtotime($data['start_date']))}} s/d {{date('d M Y', strtotime($data['end_date']))}}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%">
                <thead>
                    <tr class="text-center">
                        <th class="text-center">#</th>
                        <th class="px-1">Tanggal Transaksi</th>
                        <th class="px-1">No Ref / No Bukti</th>
                        <th class="px-1">Keterangan</th>
                        <th class="px-1">Debit (Rp)</th>
                        <th class="px-1">Kredit (Rp)</th>
                        <th class="px-1">Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="text-right px-1" colspan="6">Saldo Awal :</th>
                        <th class="text-right px-1">{{ $data['beginning_balance'] >= 0 ?number_format($data['beginning_balance'], 2, ',', '.'):'('.number_format($data['beginning_balance']*-1, 2, ',', '.').')' }}</th>
                    </tr>
                    @php
                        $i = 0;
                        $data['balance'] = $data['beginning_balance'];
                    @endphp
                    @foreach ($data['data'] as $value)
                        @php
                            $i++;
                            if ($data['account']->type == 0) {
                                $data['balance'] += $value->debit;
                                $data['balance'] -= $value->kredit;
                            }else{
                                $data['balance'] -= $value->debit;
                                $data['balance'] += $value->kredit;
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{$i}}</td>
                            <td class="px-1">{{ date('d M Y, H:i:s', strtotime($value->transaction_date)) }}</td>
                            <td class="px-1">{{ $value['reference_number'] }}</td>
                            <td class="px-1">{{ $value['name'] }}</td>
                            <td class="text-right px-1">{{ number_format($value->debit, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($value->kredit, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ $data['balance'] >= 0 ?number_format($data['balance'], 2, ',', '.'):'('.number_format(($data['balance']*-1), 2, ',', '.').')' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-right px-1">Saldo Akhir :</th>
                        <th class="text-right px-1">{{ $data['balance'] >= 0 ?number_format($data['balance'], 2, ',', '.'):'('.number_format($data['balance']*-1, 2, ',', '.').')' }}</th>
                    </tr>
                </tfoot>
            </table>
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