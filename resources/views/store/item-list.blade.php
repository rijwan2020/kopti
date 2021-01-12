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
        @if (!auth()->user()->isGudang())
            <div class="col-md-2 mb-3">
                <label class="form-label">Persediaan</label>
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
            @if (Auth::user()->hasRule('itemAdd'))
                <a href="{{ route('itemAdd') }}" class="btn btn-info" data-toggle="tooltip" data-state="dark" title="Tambah data barang">
                    <i class="fa fa-plus"></i>
                    Tambah
                </a>
            @endif
            @if (Auth::user()->hasRule('itemUpload'))
                <a href="{{ route('itemUpload') }}" class="btn btn-success" data-toggle="tooltip" data-state="dark" title="Upload data barang">
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
            <div class="card-header h4 text-center">Data Barang</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Qty (Kg)</th>
                            <th>Harga Jual (Rp)</th>
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
                                <td>{{ $value->code }}</td>
                                <td>{{ $value->name }}</td>
                                <td>
                                    @php
                                    if (fmod($value->qty, 1) !== 0.00) {
                                        echo number_format($value->qty, 2, ',', '.');
                                    }else{
                                        echo number_format($value->qty);
                                    }
                                    @endphp
                                </td>
                                <td class="text-right">{{ number_format($value->harga_jual, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('itemDetail'))
                                        <a href="{{ route('itemDetail', ['id' => $value->id]) }}" class="btn icon-btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Detail persediaan barang">
                                            <i class="fa fa-bars"></i>
                                        </a>
                                    @endif	
                                    @if (Auth::user()->hasRule('itemEdit'))
                                        <a href="{{ route('itemEdit', ['id' => $value->id]) }}" class="btn icon-btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Edit persediaan barang">
                                            <i class="fa fa-pen"></i>
                                        </a>
                                    @endif	
                                    @if (Auth::user()->hasRule('itemDelete'))
                                        <a href="#" class="btn btn-sm icon-btn btn-danger data-delete" data-state="dark" data-toggle="tooltip" data-placement="top" data-url="{{ route('itemDelete', ['id'=>$value->id]) }}" title="Hapus data barang" data-message="Anda yakin akan menghapus persediaan barang {{ $value->name }}?">
                                            <i class="fa fa-trash-alt"></i>
                                        </a>				
                                    @endif
                                    @if (Auth::user()->hasRule('itemCard'))
                                        <a href="{{ route('itemCard', ['id' => $value->id]) }}" class="btn icon-btn btn-dark btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Kartu Persediaan">
                                            <i class="fa fa-clipboard-list"></i>
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

@section('scripts')
    <script src="{{ asset('js/delete-data.js') }}"></script>
@endsection