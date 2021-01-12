<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Rekapitulasi Piutang</title>
</head>
<body class="px-5" style="font-size: 12pt; font-family:arial; font-weight:normal;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Rekapitulasi Piutang</h2>
            <h5 class="mb-1">Periode {{ date('d-m-Y', strtotime($data['start_date'])) }} s/d {{ date('d-m-Y', strtotime($data['end_date'])) }}</h5>
        </div>
    </div>
    <div class="row mb-1">
        <div class="col-md-12">
            Keanggotaan : {{ $data['status'] ? 'Anggota' : 'Non Anggota' }} <br>
            Wilayah : {{ $data['region']->name }}
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
                        <th>Piutang s/d {{ date('d-m-Y', strtotime('-1 day', strtotime($data['start_date']))) }} (Rp)</th>
                        <th>Penambahan (Rp)</th>
                        <th>Pengurangan (Rp)</th>
                        <th>Total Piutang (Rp)</th>
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
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->code }}</td>
                            <td class="px-1">{{ $value->name }}</td>
                            <td class="text-right px-1">{{ number_format($value->saldo_awal, 2, ',','.') }}</td>
                            <td class="text-right px-1">{{ number_format($value->penambahan, 2, ',','.') }}</td>
                            <td class="text-right px-1">{{ number_format($value->pengurangan, 2, ',','.') }}</td>
                            <td class="text-right px-1">{{ number_format($value->saldo_akhir, 2, ',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-right">
                        <th class="px-1" colspan="3">Jumlah</th>
                        <th class="px-1">{{ number_format($data['data']->sum('saldo_awal'), 2, ',','.') }}</th>
                        <th class="px-1">{{ number_format($data['data']->sum('penambahan'), 2, ',','.') }}</th>
                        <th class="px-1">{{ number_format($data['data']->sum('pengurangan'), 2, ',','.') }}</th>
                        <th class="px-1">{{ number_format($data['data']->sum('saldo_akhir'), 2, ',','.') }}</th>
                    </tr>
                </tfoot>
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