@extends('layouts.application')

@section('module', 'Data Barang')

@section('content')
@if ($data['data']->count() > 0)
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
            <a href="{{ route('itemUpload', ['confirm' => 1]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Konfirmasi upload">
                <i class="fa fa-check"></i>
                Konfirmasi
            </a>
            <a href="{{ route('itemUpload', ['confirm' => 0]) }}" class="btn my-1 btn-danger" data-toggle="tooltip" data-state="dark" title="Batalkan upload">
                <i class="fa fa-times"></i>
                Batalkan
            </a>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Preview Data Upload Barang</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Suplier</th>
                            <th>Tanggal Beli</th>
                            <th>Harga Beli Satuan (Rp)</th>
                            <th>Harga Jual (Rp)</th>
                            <th>Qty Pusat (Kg)</th>
                            @foreach ($data['gudang'] as $item)
                                <th>Qty {{ $item->name }} (Kg)</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = ($data['data']->currentPage() - 1) * $data['data']->perPage();
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                                $qty_gudang = json_decode($value['qty_gudang']);
                            @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $value->code }}</td>
                                <td>{{ $value->name }}</td>
                                <td>{{ $value->suplier->name ?? '' }}</td>
                                <td>{{ $value->tanggal_beli }}</td>
                                <td class="text-right">{{ number_format($value['harga_beli'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['harga_jual'], 2, ',', '.') }}</td>
                                <td>{{ $value['qty_pusat'] }}</td>
                                @foreach ($qty_gudang as $item)
                                    <td>{{ $item }}</td>
                                @endforeach
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
@else
    <div class="card">
        <div class="card-header h4 text-center">Form Upload Data Barang</div>
        <form action="{{ route('itemUploadSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
            <div class="card-body">
                @csrf
                <div class="row">
                    <div class="col-xl-10 offset-xl-1">

                        <div class="form-group">
                            <label class="form-label">File Attachment</label>
                            <div class="input-group file-input">
                                <label class="custom-file">
                                    <input type="file" class="custom-file-input upload {{ $errors->has('file')?' is-invalid':'' }}" data-target="input-file" name="file">
                                    <span class="custom-file-label" id="input-file"></span>
                                </label>
                            </div>
                            {!! $errors->first('file', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">File harus berformat .xls / .xslx</small>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Upload</button>
                <a href="{{ route('itemUploadFormat') }}" class="btn btn-success" title="download format" data-toggle="tooltip" data-state="dark"><span class="fa fa-download"></span> Download Format</a>
            </div>
        </form>
    </div>
@endif
@endsection

@section('scripts')
    <script src="{{ asset('js/file-upload.js') }}"></script>
@endsection