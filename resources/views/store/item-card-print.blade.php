<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Kartu Persediaan</title>
    <style type="text/css" media="print">
        @page { 
            size: landscape;
        }
    </style>
</head>
<body class="px-5"  style="font-size: 12pt; font-family:arial; font-weight:normal;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">{{ $data['title'] }}</h2>
            <h6 class="mb-2">{{ date('d M Y', strtotime($data['start_date'])) }} s/d {{ date('d M Y', strtotime($data['end_date'])) }}</h6>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">Nama Barang</div>
        <div class="col-sm-10">: {{ $data['item']->name }}</div>
    </div>
    <div class="row">
        <div class="col-sm-2">Kode Barang</div>
        <div class="col-sm-10">: {{ $data['item']->code }}</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%" style="font-size: 12px;">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>No Ref</th>
                        <th>Keterangan</th>
                        <th>Masuk (Kg)</th>
                        <th>Keluar (Kg)</th>
                        <th>Jumlah (Kg)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th colspan="6" class="text-right px-1">Stok s/d {{ date('d-M-Y', strtotime('-1 day', strtotime($data['start_date']))) }} :</th>
                        <th class="text-right px-1">{{ number_format($data['persediaan_awal'], 0, ',', '.') }}</th>
                    </tr>
                    @php
                        $i = 0;
                        $total_stok = $data['persediaan_awal'];
                    @endphp
                    @foreach ($data['data'] as $value)
                        @php
                            $i++;
                            if ($value->tipe) {
                                $stok_masuk = 0;
                                $stok_keluar = $value->qty;
                            }else{
                                $stok_masuk = $value->qty;
                                $stok_keluar = 0;
                            }
                            $total_stok += $stok_masuk - $stok_keluar;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value['tanggal_transaksi'] }}</td>
                            <td class="px-1">{{ $value['no_ref'] }}</td>
                            <td class="px-1">{{ $value['keterangan'] }}</td>
                            <td class="text-right px-1">{{ number_format($stok_masuk, 0, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($stok_keluar, 0, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($total_stok, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right px-1" colspan="6">Stok Akhir :</th>
                        <th class="text-right px-1">{{ number_format($total_stok, 0, ',', '.')  }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="row text-center mt-3">
        <div class="col-md-6">Mengetahui,</div>
        <div class="col-md-6">Kuningan, {{ date('d F Y', strtotime($data['end_date'])) }}</div>
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
		setTimeout(window.close, 3000);
        window.print();
    </script>
</body>
</html>