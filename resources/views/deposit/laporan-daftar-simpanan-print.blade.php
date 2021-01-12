<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Daftar Simpanan Anggota</title>
    <style type="text/css" media="print">
        @page { 
            size: landscape;
        }
    </style>
</head>
<body style="font-size: 12pt; font-family:arial; font-weight:normal;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Daftar Simpanan Anggota</h2>
            <h6 class="mb-2">Per {{ $data['end_date'] }}</h6>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%">
                <thead>
                    <tr class="text-center">
                        <th class="text-center">#</th>
                        <th width='10%'>Wilayah</th>
                        @foreach ($data['jenis'] as $item)
                            <th class="px-1">{{ $item->name }} (Rp)</th>
                        @endforeach
                        <th class="px-1">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = $jml_total = 0;
                        foreach ($data['jenis'] as $hasil) {
                            $jml[$hasil->id] = 0;
                        }
                    @endphp
                    @foreach ($data['data'] as $value)
                        @php
                            $i++;
                            $total = 0;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value['nama'] }}</td>
                            @foreach ($data['jenis'] as $item)
                                    @php
                                        $total+= $value[$item->id];
                                        $jml_total += $value[$item->id];
                                        $jml[$item->id] += $value[$item->id];
                                    @endphp
                                <td class="text-right px-1">{{ number_format($value[$item->id], 2, ',', '.') }}</td>
                            @endforeach
                            <th class="text-right px-1">{{ number_format($total, 2, ',', '.') }}</th>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-right">
                        <th colspan="2" class="px-1">Jumlah : </th>
                        @foreach ($data['jenis'] as $item)
                            <th class="px-1">{{ number_format($jml[$item->id], 2, ',', '.') }}</th>
                        @endforeach
                        <th class="px-1">{{ number_format($jml_total, 2, ',', '.') }}</th>
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