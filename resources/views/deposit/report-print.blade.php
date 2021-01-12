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
<body style="font-family:arial; font-weight:normal;">
    <div class="row mb-2">
        <div class="col-md-1 text-center">
            <img src="{{ !isset($data['data']['logo']) || $data['data']['logo']==''?asset('storage/logo.png'):asset('storage/'.$data['data']['logo']) }}" alt="" width="100">
        </div>
        <div class="col-md-10 text-center">
            <h3 class="mb-1">{{ config('koperasi.nama') }}</h3>
            <h2 class="mb-1">Rekapitulasi Simpanan</h2>
            <h6 class="mb-2">Per {{ $data['end_date'] }}</h6>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%" style="font-size: 12px;">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th width='10%'>Wilayah</th>
                        @foreach ($data['jenis'] as $item)
                            <th class="px-1">{{ $item->name }}</th>
                        @endforeach
                        <th class="px-1">Total</th>
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
                                <td class="text-right px-1">Rp{{ number_format($value[$item->id], 2, ',', '.') }}</td>
                            @endforeach
                            <th class="text-right px-1">Rp{{ number_format($total, 2, ',', '.') }}</th>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-right">
                        <th colspan="2" class="px-1">Jumlah : </th>
                        @foreach ($data['jenis'] as $item)
                            <th class="px-1">Rp{{ number_format($jml[$item->id], 2, ',', '.') }}</th>
                        @endforeach
                        <th class="px-1">Rp{{ number_format($jml_total, 2, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <script>
		setTimeout(window.close, 300);
        window.print();
    </script>
</body>
</html>