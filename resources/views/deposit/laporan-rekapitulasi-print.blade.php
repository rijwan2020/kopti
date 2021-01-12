<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Rekaptulasi Simpanan</title>
    <style type="text/css" media="print">
        @page { 
            size: landscape;
        }
    </style>
</head>
<body class="px-5" style="font-size: 12pt; font-family:arial; font-weight:normal;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">
                @if ($data['type_id'] != 'all')
                    @foreach ($data['jenis'] as $item)
                        @if ($item->id == $data['type_id'])
                            Rekapitulasi {{ $item->name }}
                        @endif
                    @endforeach
                @else
                    Rekapitulasi Simpanan
                @endif
            </h2>
            <h6 class="mb-2">Per {{ date('d-m-Y', strtotime($data['date'])) }}</h6>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>Wilayah</th>
                        <th>Saldo s/d {{ date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))) }} (Rp)</th>
                        <th>Saldo Masuk (Rp)</th>
                        <th>Saldo Keluar (Rp)</th>
                        <th>Jasa (Rp)</th>
                        <th>Total Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = $total_debit = $total_kredit = $total_saldo_awal = $total_jasa = 0;
                    @endphp
                    @foreach ($data['data'] as $value)
                        @php
                            $i++;
                            $saldo = $value['saldo_awal'] + $value['kredit'] - $value['debit'] + $value['jasa'];
                            $total_saldo_awal += $value['saldo_awal'];
                            $total_kredit += $value['kredit'];
                            $total_debit += $value['debit'];
                            $total_jasa += $value['jasa'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value['nama'] }}</td>
                            <td class="text-right px-1">{{ number_format($value['saldo_awal'], 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($value['kredit'], 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($value['debit'], 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($value['jasa'], 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($saldo, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-right">
                        <th class="px-1" colspan="2">Jumlah :</th>
                        <th class="px-1">{{ number_format($total_saldo_awal, 2, ',', '.') }}</th>
                        <th class="px-1">{{ number_format($total_kredit, 2, ',', '.') }}</th>
                        <th class="px-1">{{ number_format($total_debit, 2, ',', '.') }}</th>
                        <th class="px-1">{{ number_format($total_jasa, 2, ',', '.') }}</th>
                        <th class="px-1">{{ number_format($total_saldo_awal + $total_kredit - $total_debit + $total_jasa, 2, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="row text-center mt-3">
        <div class="col-md-9">Mengetahui,</div>
        <div class="col-md-3">Kuningan, {{ date('d F Y', strtotime($data['date'])) }}</div>
        <div class="col-md-3">
            <div>Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['ketua'] ? $data['assignment']['ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-3">
            <div>Sekretaris</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['sekretaris'] ? $data['assignment']['sekretaris'] : '_____________________' }} )</div>
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
		setTimeout(window.close, 3000);
        window.print();
    </script>
</body>
</html>