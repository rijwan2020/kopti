<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>BUKTI PEMBAYARAN PIUTANG</title>
</head>
<body class="px-5" style="font-size: 12pt">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-sm-12 text-center">
            <h2 class="mb-1">BUKTI PEMBAYARAN PIUTANG</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">Diterima dari</div>
        <div class="col-sm-4">: [{{ $data['data']->member->code }}] - {{ $data['data']->member->name }}</div>
        <div class="col-sm-3 text-right">No Ref</div>
        <div class="col-sm-3">: {{ $data['data']->no_ref }}</div>
    </div>
    <div class="row">
        <div class="col-sm-2">Alamat</div>
        <div class="col-sm-4">: {{ $data['data']->member->region->name }}</div>
        <div class="col-sm-3 text-right">Tanggal Transaksi</div>
        <div class="col-sm-3">: {{ $data['data']->trxdate }}</div>
    </div>
    <div class="row">
        <div class="col-sm-2">Keterangan</div>
        <div class="col-sm-10">: {{ $data['data']->note }}</div>
    </div>
    <div class="row">
        <div class="col-sm-2">Jumlah</div>
        <div class="col-sm-10">: Rp{{ number_format($data['data']->total) }}</div>
    </div>
    <div class="row">
        <div class="col-sm-2">Terbilang</div>
        <div class="col-sm-10">: <i>{{ ucwords($data['bilangan']) }} Rupiah</i></div>
    </div>

    
    <div class="row text-center mt-3">
        <div class="col-md-8">Mengetahui,</div>
        <div class="col-md-4">Kuningan, {{ date('d F Y', strtotime($data['data']->trxdate)) }}</div>
        <div class="col-md-4">
            <div>Bendahara</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['bendahara'] ? $data['assignment']['bendahara'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-4">
            <div>Manager</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['manager'] ? $data['assignment']['manager'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-4">
            <div>Penerima</div>
            <div style="margin-top: 100px;">( _____________________ )</div>
        </div>
    </div>

    <hr style="border-color: black">
    <script>
        window.print();
		setTimeout(window.close, 3000);
    </script>
</body>
</html>