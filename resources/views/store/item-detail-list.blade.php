@extends('layouts.application')

@section('module', 'Data Barang')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between mb-0">
                            <div>Kode Barang</div>
                            <b> {{ $data['item']->code }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Nama Barang</div>
                            <b> {{ $data['item']->name }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Harga Jual</div>
                            <b> Rp{{ number_format($data['item']->harga_jual, 2, ',', '.') }}</b>
                        </div>
                    </div>
                    <div class="col-md-8 text-right">
                        <h2 class="mb-1">
                            Stok Total: 
                            @php
                            if (fmod($data['qty'], 1) !== 0.00) {
                                echo number_format($data['qty'], 2, ',', '.'); 
                            }else{
                                echo number_format($data['qty']);
                            }
                            @endphp
                            Kg
                        </h2>
                        {{-- <div>
                            @if (Auth::user()->hasRule('depositDetailAdd'))
                                <a href="{{ route('depositDetailAdd', ['id' => $data['deposit']->id]) }}" class="btn my-1 btn-primary" data-toggle="tooltip" data-state="dark" title="Tambah Transaksi">
                                    <i class="fa fa-plus"></i>
                                    Transaksi
                                </a>
                            @endif
                            @if (Auth::user()->hasRule('depositDetailPrint'))
                                <a href="{{ route('depositDetailPrint', ['id' => $data['deposit']->id, 'q' => $data['q'], 'start_date' => $data['start_date'], 'end_date' => $data['end_date']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print Semua Transaksi" target="_blank">
                                    <i class="fa fa-print"></i>
                                    Print
                                </a>
                            @endif
                            @if (Auth::user()->hasRule('depositDetailDownload'))
                                <a href="{{ route('depositDetailDownload', ['id' => $data['deposit']->id, 'q' => $data['q'], 'start_date' => $data['start_date'], 'end_date' => $data['end_date']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download Transaksi">
                                    <i class="fa fa-download"></i>
                                    Download
                                </a>
                            @endif
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        @if (!auth()->user()->isGudang())
            <div class="col-md-2 mb-3">
                <label class="form-label">Lihat Persediaan</label>
                <select class="select2 form-control" name="warehouse_id">
                    <option value="all" {{ $data['warehouse_id'] == 'all'?'selected' : '' }}>Semua</option>
                    <option value="0" {{ $data['warehouse_id'] == '0'?'selected' : '' }}>Pusat</option>
                    @foreach ($data['warehouse'] as $value)
                        <option value="{{ $value->id }}" {{ $data['warehouse_id'] == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-md-3 mb-3">
            <label class="form-label">Stok</label>
            <div class="input-group">
                <select name="stok" id="" class="form-control select2">
                    <option value="ada" {{$data['stok'] == 'ada'?'selected':''}}>Stok Ada</option>
                    <option value="kosong" {{$data['stok'] == 'kosong'?'selected':''}}>Stok Kosong</option>
                    <option value="semua" {{$data['stok'] == 'semua'?'selected':''}}>--Semua--</option>
                </select>
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Filter</button>
                </span>
            </div>
        </div>

        <div class="col-md text-right">
            @if (Auth::user()->hasRule('purchaseAdd'))
                <a href="{{ route('purchaseAdd') }}" class="btn btn-info" data-toggle="tooltip" data-state="dark" title="Tambah data stok">
                    <i class="fa fa-plus"></i>
                    Tambah Stok
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Data Stok {{ $data['item']->name }}</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Tanggal Masuk</th>
                            <th>Gudang</th>
                            <th>Suplier</th>
                            <th>Harga Beli/Kg (Rp)</th>
                            <th>Qty (Kg)</th>
                            {{-- <th>Tanggal Kadaluarsa</th> --}}
                            <th class="text-center">Action</th>
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
                                <td>{{ $i }}</td>
                                <td>{{ $value->tanggal_masuk }}</td>
                                <td>{{ $value->warehouse->name ?? 'Pusat' }}</td>
                                <td>{{ $value->suplier->name ?? 'Tidak ada suplier' }}</td>
                                <td>{{ number_format($value->harga_beli,2, ',', '.') }}</td>
                                <td>
                                    @php
                                    if (fmod($value->qty, 1) !== 0.00) {
                                        echo number_format($value->qty, 2, ',', '.');
                                    }else{
                                        echo number_format($value->qty);
                                    }
                                    @endphp
                                </td>
                                {{-- <td>{{ $value->tanggal_kadaluarsa }}</td> --}}
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('itemDistribution') && $value->qty > 0)
                                        <a href="{{ route('itemDistribution', ['id' => $value->id]) }}" class="btn my-1 btn-{{ $value->warehouse_id == 0 ? 'dark' : 'info' }} icon-btn btn-sm" data-toggle="tooltip" data-state="dark" title="Distribusi Barang" >
                                            <i class="fa fa-truck"></i>
                                        </a>
                                    @endif
                                    @if (Auth::user()->hasRule('itemDistribution') && $value->qty > 0)
                                        <a href="{{ route('purchaseReturAdd', ['id' => $value->id]) }}" class="btn my-1 btn-warning icon-btn btn-sm" data-toggle="tooltip" data-state="dark" title="Retur Barang" >
                                            <i class="fa fa-retweet"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        Total Record : <strong>{{$data['data']->count()}}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    
@endsection