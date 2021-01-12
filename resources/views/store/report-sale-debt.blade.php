@extends('layouts.application')

@section('module', 'Laporan Toko')

@section('content')
@if (!empty($data['item_id']))
    <div class="ui-bordered px-3 pt-3 mb-3">
        <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
            <div class="col-md-3 mb-3">
                <label for="" class="form-label">Pilih Barang</label>
                <select name="item_id" id="" class="select2 form-control">
                    @foreach ($data['item'] as $item)
                        <option value="{{ $item->id }}" {{ $data['item_id'] == $item->id ? 'selected' : '' }}>[{{ $item->code }}] - {{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Keanggotaan</label>
                <select class="select2 form-control" name="member">
                    <option value="1" {{$data['member'] == 1?'selected':''}}>Anggota</option>
                    <option value="0" {{$data['member'] == 0?'selected':''}}>Non Anggota</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Filter Tanggal</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text">From</div>
                    <input type="text" class="form-control datepicker" name="start_date" value="{{$data['start_date']}}">
                    <div class="input-group-prepend"><span class="input-group-text">To</div>
                    <input type="text" class="form-control datepicker" name="end_date" value="{{$data['end_date']}}">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                    <span class="input-group-append">
                        <button class="btn btn-secondary" type="submit">Filter</button>
                    </span>
                </div>
            </div>

            <div class="col-md-3 text-right">
                <a href="{{ route('storeReportSaleDebtPrint', [$data['param']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
                <a href="{{ route('storeReportSaleDebtDownload', [$data['param']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0">{{ $data['title'] }}</h4>
                    <h5 class="mb-0">Periode {{ date('d-m-Y', strtotime($data['start_date'])) }} s/d {{ date('d-m-Y', strtotime($data['end_date'])) }}</h5>
                </div> 
                <div class="card-body py-1">
                    <div class="row">
                        <div class="col-md-3 d-flex justify-content-between">
                            <div>Kode Barang</div>
                            <b>{{ $data['barang']->code }}</b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 d-flex justify-content-between">
                            <div>Nama Barang</div>
                            <b>{{ $data['barang']->name }}</b>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-bordered">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Kode Anggota</th>
                                <th>Nama Anggota</th>
                                <th>Tanggal Transaksi</th>
                                <th>No Faktur</th>
                                <th>Status Pembayaran</th>
                                <th>Qty (Kg)</th>
                                <th>Harga (Rp)</th>
                                <th>Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-right">
                                <th colspan="6">Saldo {{ date('d-m-Y', strtotime('-1 day', strtotime($data['start_date']))) }} :</th>
                                <th>{{ number_format($data['qty'], 2, ',', '.') }}</th>
                                <th colspan="2">{{ number_format($data['saldo'], 2, ',', '.') }}</th>
                            </tr>
                            @php
                                $i = 0;
                            @endphp
                            @foreach ($data['data'] as $value)
                                @php
                                    $i++;
                                    $data['qty'] += $value->qty;
                                    $data['saldo'] += $value->harga_total_satuan;
                                @endphp
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $value->sale->member->code }}</td>
                                    <td>{{ $value->sale->member->name }}</td>
                                    <td>{{ date('d-m-Y', strtotime($value->sale->tanggal_jual)) }}</td>
                                    <td>{{ $value->sale->no_faktur }}</td>
                                    <td><span class="badge badge-{{ $value->sale->status_pembayaran == 1 ? 'success' : 'danger' }}">{{ $value->sale->status_pembayaran == 1 ? 'Lunas' : 'Belum Lunas' }}</span></td>
                                    <td class="text-right">{{ number_format($value->qty, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($value->harga_jual, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($value->harga_total_satuan, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="text-right">
                                <th colspan="6">Jumlah :</th>
                                <th>{{ number_format($data['qty'], 2, ',', '.') }}</th>
                                <th colspan="2">{{ number_format($data['saldo'], 2, ',', '.') }}</th>
                            </tr>
                        </tfoot>
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
@else
    <div class="card">
        <div class="card-header h4 text-center">Filter Laporan Penjualan Kredit</div>
        <form action="{{ url()->current() }}" enctype="multipart/form-data" class="form-input" autocomplete="false" method="GET">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-10 offset-xl-1">

                        <div class="form-group">
                            <label for="" class="form-label">Nama Barang</label>
                            <select name="item_id" id="" class="form-control select2" required>
                                @foreach ($data['item'] as $item)
                                    <option value=""></option>
                                    <option value="{{ $item->id }}">[{{ $item->code }}] - {{ $item->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Pilih Barang</small>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">Filter Tanggal</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">From</div>
                                <input type="text" class="form-control datepicker" name="start_date" value="{{$data['start_date']}}">
                                <div class="input-group-prepend"><span class="input-group-text">To</div>
                                <input type="text" class="form-control datepicker" name="end_date" value="{{$data['end_date']}}">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">Keanggotaan</label>
                            <select name="member" id="" class="form-control select2" required>
                                <option value=""></option>
                                <option value="1">Anggota</option>
                                <option value="0">Non Anggota</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Submit</button>
            </div>
        </form>
    </div>
@endif
@endsection