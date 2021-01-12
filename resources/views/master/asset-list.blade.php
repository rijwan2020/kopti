@extends('layouts.application')

@section('module', 'Data Aset Barang')

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
        <div class="col-md-2 mb-3">
            <label class="form-label">Kategori</label>
            <select class="select2 form-control" name="cat_id">
                <option value="all" {{$data['cat_id'] == 'all' ? 'selected' : ''}}>--Semua--</option>
                @foreach ($data['category'] as $item)
                    <option value="{{$item->id}}" {{$data['cat_id'] == $item->id ? 'selected' : ''}}>{{$item->name}}</option>  
                @endforeach
            </select>
        </div>
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
            @if (Auth::user()->hasRule('assetCategoryList'))
                <a href="{{ route('assetCategoryList') }}" class="btn my-1 btn-primary" data-toggle="tooltip" data-state="dark" title="Manage Kategori">
                    <i class="fa fa-cog"></i>
                    Manage Kategori
                </a>
            @endif
            @if (Auth::user()->hasRule('assetAdd'))
                <a href="{{ route('assetAdd') }}" class="btn my-1 btn-primary" data-toggle="tooltip" data-state="dark" title="Tambah data aset barang">
                    <i class="fa fa-plus"></i>
                    Tambah
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Data Aset Barang</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>QTY</th>
                            <th>Harga Beli</th>
                            <th>Tanggal Beli</th>
                            <th>Nilai Tahun Ini</th>
                            <th>Keterangan</th>
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
                                <td>{{ $value->name }}</td>
                                <td>{{ $value->category->name }}</td>
                                <td>{{ $value->qty }}</td>
                                <td>Rp{{ number_format($value->price, 2, ',', '.') }}</td>
                                <td>{{ $value->purchase_date }}</td>
                                <td>Rp{{ number_format($value->item_value, 2, ',', '.') }}</td>
                                <td>{{ $value->note }}</td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('assetEdit'))
                                        <a href="{{ route('assetEdit', ['id' => $value->id]) }}" class="btn icon-btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Edit data aset">
                                            <i class="fa fa-pen"></i>
                                        </a>
                                    @endif	
                                    @if (Auth::user()->hasRule('assetDelete') && $value->default==0)
                                        <a href="#" class="btn btn-sm icon-btn btn-danger data-delete" data-state="dark" data-toggle="tooltip" data-placement="top" data-url="{{ route('assetDelete', ['id'=>$value->id]) }}" title="Hapus data aset" data-message="Anda yakin akan menghapus data aset : {{$value->name}}?">
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
@section('scripts')
    <script src="{{ asset('js/delete-data.js') }}"></script>
@endsection