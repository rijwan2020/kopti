@extends('layouts.application')

@section('module', 'Jurnal Transaksi')

@section('styles')
    <style>
        table.table tbody tr td {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        @if (isset($data['tbb_id']))
            <input type="hidden" name="tbb_id" value="{{ $data['tbb_id'] }}">
        @endif
        @if (isset($data['tbt_id']))
            <input type="hidden" name="tbt_id" value="{{ $data['tbt_id'] }}">
        @endif
        <div class="col-md-1 mb-3">
            <label class="form-label">Limit</label>
            <select class="select2 form-control" name="limit">
                <option value="25" {{ $data['limit'] == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $data['limit'] == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $data['limit'] == 100 ? 'selected' : '' }}>100</option>
                <option value="150" {{ $data['limit'] == 150 ? 'selected' : '' }}>150</option>
                <option value="200" {{ $data['limit'] == 200 ? 'selected' : '' }}>200</option>
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
        <div class="col-md-2 mb-3">
            <label class="form-label">Pencarian</label>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Kata Kunci" name="q" value="{{$data['q']}}">
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Cari</button>
                </span>
                @if (!empty($data['q']))
                    <span class="input-group-append">
                        @if (isset($data['tbb_id']))
                            <a class="btn btn-danger" href="{{ url()->current() }}?tbb_id={{ $data['tbb_id'] }}"><i class="fa fa-times"></i></a>
                        @elseif(isset($data['tbt_id']))
                        <a class="btn btn-danger" href="{{ url()->current() }}?tbt_id={{ $data['tbt_id'] }}"><i class="fa fa-times"></i></a>
                        @else
                            <a class="btn btn-danger" href="{{ url()->current() }}"><i class="fa fa-times"></i></a>
                        @endif
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md text-right">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="mb-1">
                        Total Debit: <b>Rp{{number_format($data['jumlah']['debit'], 2, ',','.')}}</b> <br>
                        Total Kredit: <b>Rp{{number_format($data['jumlah']['kredit'], 2, ',','.')}}</b>
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if (Auth::user()->hasRule('journalAdd') && !isset($data['tbb_id']) && !isset($data['tbt_id']))
                        <a href="{{ route('journalAdd') }}" class="btn btn-info mb-3" data-toggle="tooltip" data-state="dark" data-placement="bottom" data-original-title="Tambah Jurnal Transaksi">
                            <i class="fa fa-plus"></i>
                            Tambah
                        </a>   
                    @endif
                    @if (Auth::user()->hasRule('journalPrint'))
                        <a href="{{ route('journalPrint', $data['param']) }}" class="btn btn-dark mb-3" data-toggle="tooltip" data-state="dark" data-placement="bottom" data-original-title="Print Jurnal Transaksi" target="_blank">
                            <i class="fa fa-print"></i>
                            Print
                        </a>   
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-1">Jurnal Transaksi</h4>
                <h6 class="mb-1">{{date('d M Y', strtotime($data['start_date']))}} s/d {{date('d M Y', strtotime($data['end_date']))}}</h6>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>#</th>
                            <th>No Ref / No Bukti</th>
                            <th>Tanggal Transaksi</th>
                            <th>Rincian</th>
                            <th>Debit (Rp)</th>
                            <th>Kredit (Rp)</th>
                            @if (!isset($data['tbb_id']) && !isset($data['tbt_id']))
                                <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = ($data['data']->currentPage() - 1) * $data['data']->perPage();
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                                $hasil = collect($value->detail)->sortBy('debit')->reverse();
                            @endphp
                            <tr style="{{ $value->deleted_by != 0 ? 'background-color: #ffcccc':($value->edited == 1 ? 'background-color: #ecffe6':'')}}">
                                <td class="text-center">{{ $i }}</td>
                                <td>{{ $value->reference_number }}</td>
                                <td>
                                    {{ date('d M Y H:i:s' ,strtotime($value->transaction_date)) }} <br><br>
                                    Diinput oleh: <b>{!!$value->userInput->name ?? '-'!!}</b>
                                    @if ($value->deleted_by != 0)
                                        <br>Dihapus Oleh: <b>{!!$value->userDelete->name ?? '-'!!}</b>
                                    @else
                                        @if ($value->edited == 1)
                                            <br>Diupdate Oleh: <b>{!!$value->userEdit->name ?? '-'!!}</b>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div><b>{{ $value->name }}</b></div>
                                    <div>
                                        @foreach ($hasil as $result)
                                            [{{ $result['account']['code'] }}] - {{$result['account']['name']}} <br>
                                        @endforeach
                                    </div>
                                    <div>
                                        @switch($value->type)
                                            @case(1)
                                                <span class="badge badge-danger">Pengeluaran</span>
                                                @break

                                            @case(2)
                                                <span class="badge badge-dark">Penyesuaian</span>
                                                @break
                                        
                                            @default
                                                    <span class="badge badge-primary">Pemasukan</span>
                                        @endswitch
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div>&nbsp;</div>
                                    <div>
                                        @foreach ($hasil as $result)
                                            {{number_format($result['debit'], 2, ',','.')}} <br>
                                        @endforeach
                                    </div>
                                    <div>&nbsp;</div>
                                </td>
                                <td class="text-right">
                                    <div>&nbsp;</div>
                                    <div>
                                        @foreach ($hasil as $result)
                                            {{number_format($result['kredit'], 2, ',','.')}} <br>
                                        @endforeach
                                    </div>
                                    <div>&nbsp;</div>
                                </td>
                                @if (!isset($data['tbb_id']) && !isset($data['tbt_id']))
                                    <td class="text-center">
                                        @if ($value->deleted_by == 0)
                                            @if (Auth::user()->hasRule('journalEdit') && $value->edited == 0 && $value->unit == 0)
                                                <a href="{{ route('journalEdit', ['id' => $value->id]) }}" class="btn icon-btn btn-info btn-sm" data-state="dark" data-toggle="tooltip" data-placement="bottom" title="Edit jurnal transaksi {{$value->reference_number}}">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endif
                                            @if (Auth::user()->hasRule('journalDelete') && $value->unit == 0)
                                                <a href="#" class="btn btn-sm icon-btn btn-danger data-delete" data-state="dark" data-toggle="tooltip" data-placement="top" data-url="{{ route('journalDelete', ['id'=>$value->id]) }}" title="Hapus jurnal transaksi {{$value->reference_number}}" data-message="Anda yakin akan menghapus jurnal transaksi : {{$value->reference_number}}?">
                                                    <i class="fa fa-trash-alt"></i>
                                                </a>				
                                            @endif
                                        @endif
                                        
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
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
@if ($data['session'] != null)
    @isset ($data['print'])
        <div class="modal fade" id="transaction-print" tabindex="-1" role="dialog" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Jurnal Transaksi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama" class="form-label">Tanggal Transaksi</label>
                            <input type="text" class="form-control" disabled value="{{ date('d M Y - H:i:s', strtotime($data['print']->transaction_date)) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nama">No. Ref / No. Bukti</label>
                            <input type="text" class="form-control" disabled value="{{ $data['print']->reference_number }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nama">Keterangan</label>
                            <input type="text" class="form-control" disabled value="{{ $data['print']->name }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nama">Tipe Transaksi</label>
                            <input type="text" class="form-control" disabled value="{{ $data['print']->type==0?'Pemasukan':($data['print']->type==1?'Pengeluaran':'Penyesuaian') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nama">Jumlah</label>
                            <input type="text" class="form-control" disabled value="Rp{{number_format($data['print']->amount, 2, ',', '.')}}">
                        </div>
                    </div>
                        
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    @endisset
@endif
@endsection

@section('scripts')
    <script src="{{ asset('js/delete-data.js') }}"></script>
    @isset ($data['print'])
	    <script>
			$(document).ready(function () {
				$('#transaction-print').modal('show');
				$(document).on('click', '.modal-close', function () {
	                $('#transaction-print').modal('hide');
	            });
			});
				
		</script>
	@endisset
@endsection