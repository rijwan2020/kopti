<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Jurnal Transaksi</title>
</head>
<body class="px-5" style="font-size: 12pt">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Jurnal Transaksi</h2>
            <h5 class="mb-2"> {{date('d-m-Y', strtotime($data['start_date']))}} s/d {{date('d-m-Y', strtotime($data['end_date']))}}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%">
                <thead>
                    <tr class="text-center">
                        <th class="text-center">#</th>
                        <th class="px-1">No Ref / No Bukti</th>
                        <th class="px-1">Tanggal Transaksi</th>
                        <th class="px-1">Rincian</th>
                        <th class="px-1">Debit (Rp)</th>
                        <th class="px-1">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = $debit = $kredit = 0;
                    @endphp
                    @foreach ($data['data'] as $value)
                        @if ($value->deleted_by == 0)
                            @php
                                $i++;
                                $hasil = collect($value->detail)->sortBy('debit')->reverse();
                            @endphp
                            <tr>
                                <td class="text-center">{{ $i }}</td>
                                <td class="px-1">{{ $value->reference_number }}</td>
                                <td class="px-1">
                                    {{ date('d M Y H:i:s' ,strtotime($value->transaction_date)) }}
                                </td>
                                <td class="px-1">
                                    <div><b>{{ $value->name }}</b></div>
                                    <div>
                                        @foreach ($hasil as $result)
                                            @php
                                                $debit += $result['debit'];
                                                $kredit += $result['kredit'];
                                            @endphp
                                            [{{ $result['account_code'] }}] - {{$result['account']['name']}} <br>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-right px-1">
                                    <div>&nbsp;</div>
                                    <div>
                                        @foreach ($hasil as $result)
                                            {{number_format($result['debit'], 2, ',','.')}} <br>
                                        @endforeach
                                    </div>
                                    <div>&nbsp;</div>
                                </td>
                                <td class="text-right px-1">
                                    <div>&nbsp;</div>
                                    <div>
                                        @foreach ($hasil as $result)
                                            {{number_format($result['kredit'], 2, ',','.')}} <br>
                                        @endforeach
                                    </div>
                                    <div>&nbsp;</div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <th class="text-right px-1" colspan="4">Jumlah : </th>
                        <th class="text-right px-1">{{number_format($debit, 2, ',','.')}}</th>
                        <th class="text-right px-1">{{number_format($kredit, 2, ',','.')}}</th>
                    </tr>
                </tbody>
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