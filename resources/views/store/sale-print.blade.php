<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BUKTI PENJUALAN BARANG</title>
    <link rel="stylesheet" href="{{ asset('assets/print/bootstrap3.3.7.css') }}"/>
    <script src="{{ asset('assets/print/bootstrap3.3.7.js') }}"></script>
    <script src="{{ asset('assets/print/jquery2.1.1.js') }}"></script>
    <style>
        .justify-content-between {
            justify-content: space-between !important;
        }
        .d-flex {
            display: flex !important;
        }
        .px5{
            padding-left: 3rem !important;
            padding-right: 3rem !important;
        }
        .px-1 {
            padding-left: .25rem !important;
            padding-right: .25rem !important;
        }
        .mt-3{
            margin-top: 1rem !important;
        }
    </style>
</head>
<body class="px-5" style="font-size: 12px">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-xs-12 text-center">
            <h2 class="mb-1">BUKTI PENJUALAN BARANG</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="d-flex justify-content-between px-1">
                <div>Pembeli</div>
                <div>[{{ $data['data']->member->code }}] - {{ $data['data']->member->name }}</div>
            </div>
            <div class="d-flex justify-content-between px-1">
                <div>Alamat</div>
                <div>{{ $data['data']->member->region->name }}</div>
            </div>
            <div class="d-flex justify-content-between px-1">
                <div>Keterangan</div>
                <div>{{ $data['data']->note }}</div>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="d-flex justify-content-between px-1">
                <div>No Faktur</div>
                <div>{{ $data['data']->no_faktur }}</div>
            </div>
            <div class="d-flex justify-content-between px-1">
                <div>Tanggal Transaksi</div>
                <div>{{ $data['data']->tanggal_jual }}</div>
            </div>
            <div class="d-flex justify-content-between px-1">
                <div>Transaksi di</div>
                <div>{{ $data['data']->warehouse->name ?? 'Pusat' }}</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <table border="1" width="100%">
                <thead>
                    <tr class="text-center">
                        <th class="text-center">No</th>
                        <th class="px-1">Kode Barang</th>
                        <th class="px-1">Nama Barang</th>
                        <th class="px-1">Qty (Kg)</th>
                        <th class="px-1">Harga Satuan (Rp)</th>
                        <th class="px-1">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = $total_belanja = 0;
                    @endphp
                    @foreach ($data['data']->detail as $value)
                        @php
                            $i++;
                            $total_belanja += $value->harga_total_satuan;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->item->code }}</td>
                            <td class="px-1">{{ $value->item->name }}</td>
                            <td class="text-right px-1">
                                @php
                                if (fmod($value->qty, 1) !== 0.00) {
                                    echo number_format($value->qty, 2, ',', '.');
                                }else{
                                    echo number_format($value->qty);
                                }
                                @endphp
                            </td>
                            <td class="text-right px-1">{{ number_format($value->harga_jual, 2, ',','.') }}</td>
                            <td class="text-right px-1">{{ number_format($value->harga_total_satuan, 2, ',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right px-1" colspan="5">Jumlah : </th>
                        <th class="text-right px-1">{{number_format($total_belanja, 2, ',','.')}}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="row text-center mt-3">
        <div class="col-xs-4">
            <div>Diterima dengan baik <br>oleh pembeli</div>
            <div style="margin-top: 100px;">( {{ $data['data']->member->name }} )</div>
        </div>
        <div class="col-xs-4">
            <div>Mengetahui <br> Bendahara/Manager</div>
            <div style="margin-top: 100px;">( _____________________ )</div>
        </div>
        <div class="col-xs-4">
            <div>Petugas <br>Penjualan</div>
            <div style="margin-top: 100px;">( _____________________ )</div>
        </div>
    </div>
    <script>
        window.print();
		setTimeout(window.close, 3000);
    </script>
</body>
</html>