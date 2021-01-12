<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Transaksi Pembelian</title>
</head>
<body class="px-5" style="font-size: 12pt; font-family:arial; font-weight:normal;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h3 class="mb-1">{{ config('koperasi.nama') }}</h3>
            <h2 class="mb-1">Faktur Pembelian</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="d-flex justify-content-between px-1">
                <div>No Faktur</div>
                <div>{{ $data['data']->no_faktur }}</div>
            </div>
            <div class="d-flex justify-content-between px-1">
                <div>Tanggal Transaksi</div>
                <div>{{ $data['data']->tanggal_beli }}</div>
            </div>
            <div class="d-flex justify-content-between px-1">
                <div>Suplier</div>
                <div>{{ $data['data']->suplier->name }}</div>
            </div>
            <div class="d-flex justify-content-between px-1">
                <div>Keterangan</div>
                <div>{{ $data['data']->note }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-between px-1">
                <div>Total Pembelian</div>
                <div>Rp{{ number_format($data['data']->total, 2, ',', '.') }}</div>
            </div>
            <div class="d-flex justify-content-between px-1">
                <div>Diskon</div>
                <div>Rp{{ number_format($data['data']->diskon, 2, ',', '.') }}</div>
            </div>
            <div class="d-flex justify-content-between px-1">
                <div>Total yang harus dibayar</div>
                <div>Rp{{ number_format($data['data']->total - $data['data']->diskon, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%">
                <thead>
                    <tr class="text-center">
                        <th class="text-center">NO</th>
                        <th class="px-1">Kode Barang</th>
                        <th class="px-1">Nama Barang</th>
                        <th class="px-1">Tanggal Kadaluarsa</th>
                        <th class="px-1">Qty (Kg)</th>
                        <th class="px-1">Harga Beli (Rp)</th>
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
                            $total_belanja += $value->total;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->item->code }}</td>
                            <td class="px-1">{{ $value->item->name }}</td>
                            <td class="px-1">{{ $value->tanggal_kadaluarsa }}</td>
                            <td class="text-right px-1">
                                @php
                                if (fmod($value->qty, 1) !== 0.00) {
                                    echo number_format($value->qty, 2, ',', '.');
                                }else{
                                    echo number_format($value->qty);
                                }
                                @endphp
                            </td>
                            <td class="text-right px-1">{{ number_format($value->harga_beli, 2, ',','.') }}</td>
                            <td class="text-right px-1">{{ number_format($value->total, 2, ',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right px-1" colspan="6">Jumlah : </th>
                        <th class="text-right px-1">{{number_format($total_belanja, 2, ',','.')}}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="row text-center mt-3">
        <div class="col-md-6">
            <div>Suplier</div>
            <div style="margin-top: 100px;">(_____________________)</div>
        </div>
        <div class="col-md-6">
            <div>Penerima</div>
            <div style="margin-top: 100px;">(_____________________)</div>
        </div>
    </div>
    <script>
        window.print();
		setTimeout(window.close, 3000);
    </script>
</body>
</html>