@extends('layouts.application')

@section('module', 'Simpanan')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        <div class="col-md-1 mb-3">
            <label class="form-label">Limit</label>
            <select class="select2 form-control" name="limit">
                <option value="25" {{ $data['limit'] == 25 ?' selected' : '' }}>25</option>
                <option value="50" {{ $data['limit'] == 50 ?' selected' : '' }}>50</option>
                <option value="100" {{ $data['limit'] == 100 ?' selected' : '' }}>100</option>
                <option value="150" {{ $data['limit'] == 150 ?' selected' : '' }}>150</option>
                <option value="200" {{ $data['limit'] == 200 ?' selected' : '' }}>200</option>
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Jenis</label>
            <select class="select2 form-control" name="type_id">
                <option value="all" {{ $data['type_id'] == 'all' ? 'selected' : '' }}>--Semua--</option>
                @foreach ($data['type'] as $value)
                    <option value="{{ $value->id }}" {{ $data['type_id'] == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Pencarian</label>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Kata Kunci" name="q" value="{{ $data['q'] }}">
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
            @if (Auth::user()->hasRule('depositAdd'))
                <a href="{{ route('depositAdd') }}" class="btn my-1 btn-primary" data-toggle="tooltip" data-state="dark" title="Tambah data simpanan">
                    <i class="fa fa-plus"></i>
                    Tambah
                </a>
            @endif
            @if (Auth::user()->hasRule('depositPrintAll'))
                <a href="{{ route('depositPrintAll' , ['q' => $data['q'], 'type_id' => $data['type_id']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print data simpanan" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('depositDownload'))
                <a href="{{ route('depositDownload' , ['q' => $data['q'], 'type_id' => $data['type_id']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download data simpanan">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
            @if (Auth::user()->hasRule('depositUpload'))
                <a href="{{ route('depositUpload') }}" class="btn my-1 btn-warning" data-toggle="tooltip" data-state="dark" title="Upload data simpanan">
                    <i class="fa fa-upload"></i>
                    Upload
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Data Simpanan</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>No Rekening</th>
                            <th>Wilayah</th>
                            <th>Jenis Simpanan</th>
                            <th>Tanggal Registrasi</th>
                            <th>Last Transaksi</th>
                            <th>Saldo (Rp)</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = ($data['data']->currentPage() - 1) * $data['data']->perPage();
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                            @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $value->member->code }}</td>
                                <td>{{ $value->member->name }}</td>
                                <td>{{ $value->account_number }}</td>
                                <td>{{ $value->region->name }}</td>
                                <td>{{ $value->type->name }}</td>
                                <td>{{ $value->registration_date }}</td>
                                <td>{{ $value->last_transaction }}</td>
                                <td class="text-right">{{ number_format($value->balance, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('depositDetail'))
                                        <a href="{{ route('depositDetail', ['id' => $value->id]) }}" class="btn icon-btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Detail simpanan : {{ $value->account_number }}">
                                            <i class="fa fa-bars"></i>
                                        </a>
                                    @endif	
                                    @if (Auth::user()->hasRule('depositBook'))
                                        <a href="{{ route('depositBook', ['id' => $value->id]) }}" class="btn icon-btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Buku tabungan : {{ $value->account_number }}">
                                            <i class="fa fa-book"></i>
                                        </a>
                                    @endif	
                                    @if (Auth::user()->hasRule('depositDelete'))
                                        <a href="{{ route('depositDelete', ['id'=>$value->id]) }}" class="btn btn-sm icon-btn btn-danger data-delete" data-state="dark" data-toggle="tooltip" data-placement="top" title="Hapus data simpanan">
                                            <i class="fa fa-trash-alt"></i>
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