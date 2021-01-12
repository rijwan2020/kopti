@extends('layouts.application')

@section('module', 'Data Barang')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        <div class="col-md-1 mb-3">
            <label class="form-label">Limit</label>
            <select class="select2 form-control" name="limit">
                <option value="25" {{$data['limit'] == 25?'selected':''}}>25</option>
                <option value="50" {{$data['limit'] == 50?'selected':''}}>50</option>
                <option value="100" {{$data['limit'] == 100?'selected':''}}>100</option>
                <option value="150" {{$data['limit'] == 150?'selected':''}}>150</option>
                <option value="200" {{$data['limit'] == 200?'selected':''}}>200</option>
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
            </div>
        </div>
        @if (!auth()->user()->isGudang())
            <div class="col-md-2 mb-3">
                <label class="form-label">Gudang</label>
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
            <label class="form-label">Pencarian</label>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Kata Kunci" name="q" value="{{$data['q']}}">
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Cari</button>
                </span>
                @if (!empty($data['q']))
                    <span class="input-group-append">
                        <a class="btn btn-danger" href="{{ url()->current() }}"><i class="fa fa-times"></i></a>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md text-right">
            @if (Auth::user()->hasRule('itemCardDownload'))
                <a href="{{ route('itemCardDownload', ['id' => $data['item']->id, 'start_date' => $data['start_date'], 'end_date' => $data['end_date'], 'q' => $data['q'], 'warehouse_id' => $data['warehouse_id']]) }}" class="btn btn-success" data-toggle="tooltip" data-state="dark" title="Donwload Kartu Persediaan">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
            @if (Auth::user()->hasRule('itemCardPrint'))
                <a href="{{ route('itemCardPrint', ['id' => $data['item']->id, 'start_date' => $data['start_date'], 'end_date' => $data['end_date'], 'q' => $data['q'], 'warehouse_id' => $data['warehouse_id']]) }}" class="btn btn-dark" data-toggle="tooltip" data-state="dark" title="Print Kartu Persediaan" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0">{{ $data['title'] }}</h4>
                <h5 class="mb-0">{{ date('d M Y', strtotime($data['start_date'])) }} s/d {{ date('d M Y', strtotime($data['end_date'])) }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3">Nama Barang</div>
                    <div class="col-sm-9">: {{ $data['item']->name }}</div>
                </div>
                <div class="row">
                    <div class="col-sm-3">Kode Barang</div>
                    <div class="col-sm-9">: {{ $data['item']->code }}</div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
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
                            <th colspan="6" class="text-right">Stok Sebelumnya :</th>
                            <th class="text-right">{{ number_format($data['persediaan_awal'], 0, ',', '.') }}</th>
                        </tr>
                        @php
                            $i = ($data['data']->currentPage() - 1) * $data['data']->perPage();
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
                                <td>{{ $i }}</td>
                                <td>{{ $value['tanggal_transaksi'] }}</td>
                                <td>{{ $value['no_ref'] }}</td>
                                <td>{{ $value['keterangan'] }}</td>
                                <td class="text-right">{{ number_format($stok_masuk, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($stok_keluar, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($total_stok, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-right" colspan="6">Stok Akhir :</th>
                            <th class="text-right">{{ number_format($total_stok, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        Total Record : <strong>{{$data['data']->count() + ($data['limit']*($data['data']->currentPage() - 1))}}</strong> of <strong>{{$data['data']->total()}}</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $data['data']->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

