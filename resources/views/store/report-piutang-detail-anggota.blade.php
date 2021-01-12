@extends('layouts.application')

@section('module', 'Laporan Rekapitulasi Piutang')

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
        <div class="col-md-5 mb-3">
            <label class="form-label">Filter Tanggal</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">From</div>
                <input type="text" class="form-control datepicker" name="start_date" value="{{$data['start_date']}}">
                <div class="input-group-prepend"><span class="input-group-text">To</div>
                <input type="text" class="form-control datepicker" name="end_date" value="{{$data['end_date']}}">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Cari</button>
                </span>
            </div>
        </div>

        <div class="col-md-6 text-right">
            @if (Auth::user()->hasRule('storeReportPiutangDetailAnggotaPrint'))
            <a href="{{ route('storeReportPiutangDetailAnggotaPrint', ['id' => $data['data']->id,'start_date' => $data['start_date'], 'end_date' => $data['end_date']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print" target="_blank">
                <i class="fa fa-print"></i>
                Print
            </a>
            @endif
            @if (Auth::user()->hasRule('storeReportPiutangDetailAnggotaDownload'))
                <a href="{{ route('storeReportPiutangDetailAnggotaDownload', ['id' => $data['data']->id,'start_date' => $data['start_date'], 'end_date' => $data['end_date']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0">Buku Besar Pembantu Piutang {{ $data['data']->status == 1 ? 'Anggota' : 'Non Anggota' }}</h4>
                <h5 class="mb-0">Periode {{ date('d-m-Y', strtotime($data['start_date'])) }} s/d {{ date('d-m-Y', strtotime($data['end_date'])) }}</h5>
            </div>
            <div class="card-body">
                Nama Anggota : {{ $data['data']->name }} <br>
                Wilayah : {{ $data['data']->region->name }}
            </div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Tanggal Transaksi</th>
                            <th>No Ref</th>
                            <th>Keterangan</th>
                            <th>Debit (Rp)</th>
                            <th>Kredit (Rp)</th>
                            <th>Saldo (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-right">
                            <th colspan="6">Saldo Awal</th>
                            <th>{{ number_format($data['saldo_awal'], 2, ',', '.') }}</th>
                        </tr>
                        @php
                            $i = ($data['list']->currentPage() - 1) * $data['list']->perPage();
                            $saldo = $data['saldo_awal'];
                        @endphp
                        @foreach ($data['list'] as $value)
                            @php
                                $i++;
                                if ($value->tipe) {
                                    $debit = $value->total;
                                    $kredit = 0;
                                }else{
                                    $debit = 0;
                                    $kredit = $value->total;
                                }
                                $saldo += $kredit - $debit;
                            @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $value->trxdate }}</td>
                                <td>{{ $value->no_ref }}</td>
                                <td>{{ $value->note }}</td>
                                <td class="text-right">{{ number_format($kredit, 2, ',', '.')}}</td>
                                <td class="text-right">{{ number_format($debit, 2, ',', '.')}}</td>
                                <td class="text-right">{{ number_format($saldo, 2, ',', '.')}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-right">
                            <th colspan="6">Saldo Akhir</th>
                            <th>{{ number_format($saldo, 2, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        Total Record : <strong>{{$data['list']->count() + ($data['limit']*($data['list']->currentPage() - 1))}}</strong> of <strong>{{$data['list']->total()}}</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $data['list']->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection