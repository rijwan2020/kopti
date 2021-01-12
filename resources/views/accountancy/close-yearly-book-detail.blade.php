@extends('layouts.application')

@section('module', 'Tutup Buku')
@section('content')
<div class="row">
    <div class="col-md-3">Tanggal Tutup Buku</div>
    <div class="col-md-9">: {{ date('d-m-Y, H:i:s', strtotime($data['data']->closing_date)) }}</div>
</div>
<div class="row">
    <div class="col-md-3">Periode</div>
    <div class="col-md-9">: {{ $data['data']->start_periode }} s/d {{ $data['data']->end_periode }}</div>
</div>
<div class="row">
    <div class="col-md-3">Catatan</div>
    <div class="col-md-9">: {{ $data['data']->note }}</div>
</div>
<br>
<h5>Pembukuan</h5>
<div class="row">
    <div class="col-sm-6 col-md-4 col-xl-3">
        <a href="{{ route('journalList', ['tbt_id' => $data['data']->id]) }}" class="text-dark">
            <div class="card mb-3" id="shortcut">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="ion ion-md-journal display-4 text-success"></div>
                        <div class="ml-3">
                            <div class="large"><b>Jurnal Transaksi</b></div>
                            <div class="text-large"></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-md-4 col-xl-3">
        <a href="{{ route('adjustingJournalList', ['tbt_id' => $data['data']->id]) }}" class="text-dark">
            <div class="card mb-3" id="shortcut">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="ion ion-md-journal display-4 text-warning"></div>
                        <div class="ml-3">
                            <div class="large"><b>Jurnal Penyesuaian</b></div>
                            <div class="text-large"></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-md-4 col-xl-3">
        <a href="{{ route('ledger', ['tbt_id' => $data['data']->id]) }}" class="text-dark">
            <div class="card mb-3" id="shortcut">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="ion ion-ios-book display-4 text-danger"></div>
                        <div class="ml-3">
                            <div class="large"><b>Buku Besar</b></div>
                            <div class="text-large"></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-md-4 col-xl-3">
        <a href="{{ route('trialBalance', ['tbt_id' => $data['data']->id]) }}" class="text-dark">
            <div class="card mb-3" id="shortcut">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="ion ion-md-clipboard display-4 text-primary"></div>
                        <div class="ml-3">
                            <div class="large"><b>Neraca Saldo</b></div>
                            <div class="text-large"></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>


<h5>Laporan</h5>
<div class="row">
    <div class="col-sm-6 col-md-4 col-xl-3">
        <a href="{{ route('balance', ['tbt_id' => $data['data']->id]) }}" class="text-dark">
            <div class="card mb-3" id="shortcut">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="display-4 text-dark"><i class="fa fa-balance-scale"></i></div>
                        <div class="ml-3">
                            <div class="large"><b>Neraca</b></div>
                            <div class="text-large"></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-md-4 col-xl-3">
        <a href="{{ route('balanceDescription', ['tbt_id' => $data['data']->id]) }}" class="text-dark">
            <div class="card mb-3" id="shortcut">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="ion ion-md-document display-4 text-success"></div>
                        <div class="ml-3">
                            <div class="large"><b>Penjelasan Neraca</b></div>
                            <div class="text-large"></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-md-4 col-xl-3">
        <a href="{{ route('phu', ['tbt_id' => $data['data']->id]) }}" class="text-dark">
            <div class="card mb-3" id="shortcut">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="ion ion-md-paper display-4 text-secondary"></div>
                        <div class="ml-3">
                            <div class="large"><b>Penjelasan PHU</b></div>
                            <div class="text-large"></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-md-4 col-xl-3">
        <a href="{{ route('shu', ['tbt_id' => $data['data']->id]) }}" class="text-dark">
            <div class="card mb-3" id="shortcut">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="ion ion-md-cash display-4 text-primary"></div>
                        <div class="ml-3">
                            <div class="large"><b>Sisa Hasil Usaha</b></div>
                            <div class="text-large"></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection