@extends('layouts.application')

@section('module', 'Data Akun')

@section('content')
@if ($data['set-account'] == 0)
    @if ($data['data']->count() > 0)
        <div class="ui-bordered px-3 pt-3 mb-3">
            <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
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
                    <a href="{{ route('accountConfig', ['confirm' => 1]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Konfirmasi upload">
                        <i class="fa fa-check"></i>
                        Konfirmasi
                    </a>
                    <a href="{{ route('accountConfig', ['confirm' => 0]) }}" class="btn my-1 btn-danger" data-toggle="tooltip" data-state="dark" title="Batalkan upload">
                        <i class="fa fa-times"></i>
                        Batalkan
                    </a>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header h4 text-center">Preview Upload Data Saldo Awal</div>
                    <div class="table-responsive">
                        <table class="table card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th rowspan="2" class="align-middle">#</th>
                                    <th rowspan="2" class="align-middle">Kode Akun</th>
                                    <th rowspan="2" class="align-middle">Nama Akun</th>
                                    <th rowspan="2" class="align-middle">Saldo Normal</th>
                                    <th colspan="2" class="text-center">Saldo Awal</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Debit</th>
                                    <th class="text-center">Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($data['data'] as $value)
                                    @php
                                        $i++;
                                        if ($value->type == 0) {
                                            if ($value->balance >= 0) {
                                                $debit = $value->balance;
                                                $kredit = 0;
                                            }else{
                                                $kredit = $value->balance * -1;
                                                $debit = 0;
                                            }
                                        }else{
                                            if ($value->balance >= 0) {
                                                $kredit = $value->balance;
                                                $debit = 0;
                                            }else{
                                                $debit = $value->balance * -1;
                                                $kredit = 0;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $value->code }}</td>
                                        <td>{{ $value->name }}</td>
                                        <td>{{ $value->type == 1 ?'Kredit' : 'Debit' }}</td>
                                        <td class="text-right">Rp{{ number_format($debit, 2, ',', '.')  }}</td>
                                        <td class="text-right">Rp{{ number_format($kredit, 2, ',', '.')  }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-3">
                                Total Record : {{$data['data']->count()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header h4 text-center">Form Konfigurasi Saldo Awal</div>
            <form action="{{ route('accountConfigSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
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
                    <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Save</button>
                    <a href="{{ route('accountConfig', ['download' => 1]) }}" class="btn btn-success"><span class="fa fa-download"></span> Download Format</a>
                </div>
            </form>
        </div>
    @endif
@else
    <div class="row">
        <div class="col-md-8 text-center offset-md-2">
            <div class="alert alert-dark-info fade show text-center">
                <i class="fa fa-info"></i>
                <strong>Info!</strong>  
                Saldo awal sudah di konfigurasi. Klik <b>RESET</b> untuk menghapus konfigurasi saldo awal. 
            </div>
            <div>
                <a href="#" class="btn btn-danger data-delete" data-url="{{ route('accountConfigReset') }}" title="Reset saldo awal" data-message="Anda yakin akan menghapus konfigurasi saldo awal? Semua data jurnal juga akan dihapus.">
                    Reset
                </a> 
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
    <script src="{{ asset('js/file-upload.js') }}"></script>
    <script src="{{ asset('js/delete-data.js') }}"></script>
@endsection