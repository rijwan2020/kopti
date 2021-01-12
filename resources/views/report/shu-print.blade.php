<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Sisa Hasil Usaha</title>
</head>
<body style="font-size: 12pt" class="px-5">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Rencana Pembagian Sisa Hasil Usaha</h2>
            <h5 class="mb-2">Periode {{ date('d M Y', strtotime($data['end_date'])) }}</h5>
        </div>
    </div>
    <div class="row h5 mb-0">
        <div class="col-md-6">Perhitungan Hasil Usaha</div>
        <div class="col-md-6 text-right">{{ $data['shu'] >= 0 ? number_format($data['shu'] + $data['zakat'], 2, ',', '.') : '(Rp'.number_format(($data['shu'] + $data['zakat'])*-1, 2, ',', '.').')' }}</div>
    </div>
    <div class="row h5 mb-0">
        <div class="col-md-6">Pengeluaran Zakat</div>
        <div class="col-md-6 text-right">{{ $data['shu'] >= 0 ? number_format($data['zakat'], 2, ',', '.') : 'Rp0' }}</div>
    </div>
    <div class="row h5 mb-3">
        <div class="col-md-6">Dana yang dibagikan (PHU - Zakat)</div>
        <div class="col-md-6 text-right">{{ $data['shu'] >= 0 ? number_format($data['shu'], 2, ',', '.') : 'Rp0' }}</div>
    </div>

    <table width="100%">
        <thead>
            <tr>
                <td class="px-1" colspan="4">Rencana Pembagian PHU sesuai dengan Anggaran Rumah Tangga {{ config('koperasi.nama') }} Bab XII pasal 63 sebagai berikut :</td>
            </tr>
        </thead>
        <tbody>
            @php
                $jumlah = $i = 0;
            @endphp
            @foreach ($data['data'] as $value)
                @php
                    $i++;
                    $total = $value->percent * $data['shu'] / 100;
                    $jumlah += $total;
                @endphp
                <tr>
                    <td class="text-center">{{ $i }}</td>
                    <td class="px-1">{{ number_format($value->percent, 2, ',','.') }}%</td>
                    <td class="px-1">{{ $value->allocation }}</td>
                    <td class="px-1 text-right">Rp{{ $data['shu'] >= 0 ? number_format($total, 2, ',','.') : 0 }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="text-right">
                <th colspan="3">Jumlah :</th>
                <th>Rp{{ $data['shu'] >= 0 ? number_format($jumlah, 2, ',','.') : 0 }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="row text-center mt-3">
        <div class="col-sm-9">Mengetahui,</div>
        <div class="col-sm-3">Kuningan, {{ date('d F Y', strtotime($data['end_date'])) }}</div>
        <div class="col-sm-3">
            <div>Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['ketua'] ? $data['assignment']['ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-sm-3">
            <div>Wakil Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['wakil_ketua'] ? $data['assignment']['wakil_ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-sm-3">
            <div>Bendahara</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['bendahara'] ? $data['assignment']['bendahara'] : '_____________________' }} )</div>
        </div>
        <div class="col-sm-3">
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