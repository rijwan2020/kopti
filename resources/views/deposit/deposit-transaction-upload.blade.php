@extends('layouts.application')

@section('module', 'Simpanan')
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
            <a href="{{ route('depositTransactionUpload', ['confirm' => 1]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Konfirmasi upload">
                <i class="fa fa-check"></i>
                Konfirmasi
            </a>
            <a href="{{ route('depositTransactionUpload', ['confirm' => 0]) }}" class="btn my-1 btn-danger" data-toggle="tooltip" data-state="dark" title="Batalkan upload">
                <i class="fa fa-times"></i>
                Batalkan
            </a>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Preview Data Upload Transaksi Simpanan</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Kode Anggota</th>
                            <th>Nama Anggota</th>
                            <th>Wilayah</th>
                            <th>No Rekening</th>
                            <th>Jenis Transaksi</th>
                            <th>No Ref</th>
                            <th>Keterangan</th>
                            <th>Tanggal Transaksi</th>
                            <th>Jumlah (Rp)</th>
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
                                <td>{{ $value->member->region->name }}</td>
                                <td>{{ $value->no_rekening }}</td>
                                <td>{{ $data['jenis_transaksi'][$value->jenis_transaksi] }}</td>
                                <td>{{ $value->no_ref }}</td>
                                <td>{{ $value->keterangan }}</td>
                                <td>{{ $value->tanggal_transaksi }}</td>
                                <td class="text-right">{{ number_format($value->jumlah, 2, ',', '.') }}</td>
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
<div class="card mb-3">
    <div class="card-body">
        <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
            <input type="hidden" name="download">
            <div class="col-md-2 mb-3">
                <label class="form-label">Jenis Simpanan</label>
                <select class="select2 form-control" name="type_id" required>
                    <option value="">--Semua--</option>
                    @foreach ($data['type'] as $value)
                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Wilayah</label>
                <select class="select2 form-control" name="region_id">
                    <option value="all">--Semua--</option>
                    @foreach ($data['region'] as $value)
                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-success mt-4" type="submit"><span class="fa fa-download"></span> Download Format</button>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header h4 text-center">Form Upload Transaksi Simpanan</div>
    <form action="{{ route('depositTransactionUploadSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">File Attachment</label>
                        <div class="input-group file-input">
                            <label class="custom-file">
                                <input type="file" class="custom-file-input upload {{ $errors->has('file')?' is-invalid':'' }}" data-target="input-file" name="file" required>
                                <span class="custom-file-label" id="input-file"></span>
                            </label>
                        </div>
                        {!! $errors->first('file', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">File harus berformat .xls / .xslx. Maksimal 500 baris.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal transaksi *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                            <input type="text" class="form-control datepicker {{ $errors->has('tanggal_transaksi')?' is-invalid':'' }} " name="tanggal_transaksi" value="{{ old('tanggal_transaksi') ?? date('Y-m-d') }}">
                        </div>
                        {!! $errors->first('tanggal_transaksi', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan tanggal transaksi pembuatan simpanan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Masuk Jurnal</label>
                        <div>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jurnal" value="0" {{ old('jurnal')==0 ? 'checked' : ''}}>
                                <span class="form-check-label">Tidak</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jurnal" value="1" {{ old('jurnal')==1 ? 'checked' : ''}}>
                                <span class="form-check-label">Ya</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Transaksi Ke *</label>
                        <select class="form-control select2 {{ $errors->has('akun')?' is-invalid':'' }}" name="akun">
                            @foreach ($data['cash'] as $value)
                                <option value="{{ $value->code }}" {{ $value->code == old('akun') ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('akun', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk penjurnalan jika masuk jurnal.</small>
                    </div>

                    <div class="form-group">
                        <b>CATATAN: DATA EXCEL TIDAK BOLEH KOSONG DAN SEMUA FIELD YANG DI EXCEL HARUS TERISI</b>
                    </div>

                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Upload</button>
        </div>
    </form>
</div>
@endif

@endsection

@section('scripts')
    <script src="{{ asset('js/file-upload.js') }}"></script>
@endsection