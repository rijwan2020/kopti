@extends('layouts.application')

@section('module', 'Simpanan')

@section('content')
@if ($data['data']->total() > 0)
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
                <a href="{{ route('depositBillList', ['confirm' => 1]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Konfirmasi upload">
                    <i class="fa fa-check"></i>
                    Konfirmasi
                </a>
                <a href="{{ route('depositBillList', ['confirm' => 0]) }}" class="btn my-1 btn-danger" data-toggle="tooltip" data-state="dark" title="Batalkan upload">
                    <i class="fa fa-times"></i>
                    Batalkan
                </a>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header h4 text-center">Preview Data Upload Tagihan Simpanan</div>
                <div class="table-responsive">
                    <table class="table card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Kode Anggota</th>
                                <th>Nama Anggota</th>
                                <th>Wilayah</th>
                                <th>No Rekening</th>
                                <th>Jenis Simpanan</th>
                                <th>Tanggal Transaksi</th>
                                <th>Jumlah Tagihan</th>
                                <th>Jumlah Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = ($data['data']->currentPage() - 1) * $data['data']->perPage();
                            @endphp
                            @foreach ($data['data'] as $value)
                                @php
                                    $i++;
                                    $bill = 0;
                                    if ($value->deposit->deposit_type_id == 1) {
                                        $bill = $value->deposit->bill->principal_balance - $value->deposit->balance;
                                    }
                                    if ($value->deposit->deposit_type_id == 2) {
                                        $curdate = date('Y-m-d');
                                        if ($value->deposit->bill->next_bill <= $curdate) {
                                            $timeStart = strtotime($value->deposit->bill->next_bill);
                                            $timeEnd = strtotime($curdate);
                                            // Menambah bulan ini + semua bulan pada tahun sebelumnya
                                            $numBulan = 1 + (date("Y", $timeEnd) - date("Y", $timeStart)) * 12;
                                            // menghitung selisih bulan
                                            $numBulan += date("m", $timeEnd) - date("m", $timeStart);
                                            $bill = $numBulan * $value->deposit->bill->obligatory_balance;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $value->member->code }}</td>
                                    <td>{{ $value->member->name }}</td>
                                    <td>{{ $value->member->region->name }}</td>
                                    <td>{{ $value->deposit->account_number }}</td>
                                    <td>{{ $value->deposit->type->name }}</td>
                                    <td>{{ $value->transaction_date }}</td>
                                    <td class="text-right">Rp{{ number_format($bill, 2, ',', '.') }}</td>
                                    <td class="text-right">Rp{{ number_format($value->bayar, 2, ',', '.') }}</td>
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
        <div class="card-header h4 text-center">Form Upload Tagihan Simpanan</div>
        <form action="{{ route('depositBillUpload') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
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

                        <div class="form-group">
                            <label class="form-label">Tanggal transaksi *</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                                <input type="text" class="form-control datepicker {{ $errors->has('transaction_date')?' is-invalid':'' }} " name="transaction_date" value="{{ old('transaction_date') ?? date('Y-m-d') }}">
                            </div>
                            {!! $errors->first('transaction_date', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Masukan tanggal transaksi pembuatan simpanan.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Transaksi Ke *</label>
                            <select class="form-control select2 {{ $errors->has('account')?' is-invalid':'' }}" name="account" required>
                                @foreach ($data['cash'] as $value)
                                    <option value="{{ $value->code }}" {{ $value->code == old('account') ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('account', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Pilih akun untuk penjurnalan.</small>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Upload</button>
                @if (Auth::user()->hasRule('depositBillDownload'))
                    <a href="{{ route('depositBillDownload') }}" class="btn btn-success"><span class="fa fa-download"></span> Download Tagihan</a>
                @endif
            </div>
        </form>
    </div>
@endif
@endsection

@section('scripts')
    <script src="{{ asset('js/file-upload.js') }}"></script>
@endsection