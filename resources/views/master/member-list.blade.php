@extends('layouts.application')

@section('module', 'Data Anggota')

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
            <label class="form-label">Wilayah</label>
            <select class="select2 form-control" name="region_id">
                <option value="all" {{$data['region_id'] == 'all' ? 'selected' : ''}}>--Semua--</option>
                @foreach ($data['region'] as $item)
                    <option value="{{$item->id}}" {{$data['region_id'] == $item->id ? 'selected' : ''}}>{{$item->name}}</option>  
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Status</label>
            <select class="select2 form-control" name="status">
                <option value="all" {{$data['status'] === 'all' ? 'selected' : ''}}>--Semua--</option>
                <option value="1" {{$data['status'] === '1' ? 'selected' : ''}}>Anggota Aktif</option>
                <option value="0" {{$data['status'] === '0' ? 'selected' : ''}}>Non Anggota</option>
                <option value="2" {{$data['status'] === '2' ? 'selected' : ''}}>Anggota Keluar</option>
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
            @if (Auth::user()->hasRule('memberAdd'))
                <a href="{{ route('memberAdd') }}" class="btn my-1 btn-sm btn-primary" data-toggle="tooltip" data-state="dark" title="Tambah data anggota">
                    <i class="fa fa-plus"></i>
                    Tambah
                </a>
            @endif
            @if (Auth::user()->hasRule('memberPrint'))
                <a href="{{ route('memberPrint', ['region_id' => $data['region_id'], 'status' => $data['status'], 'q' => $data['q']]) }}" class="btn my-1 btn-sm btn-dark" data-toggle="tooltip" data-state="dark" title="Print data anggota" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('memberDownload'))
                <a href="{{ route('memberDownload', ['region_id' => $data['region_id'], 'status' => $data['status'], 'q' => $data['q']]) }}" class="btn my-1 btn-sm btn-success" data-toggle="tooltip" data-state="dark" title="Download data anggota">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
            @if (Auth::user()->hasRule('memberUpload'))
                <a href="{{ route('memberUpload') }}" class="btn my-1 btn-sm btn-warning" data-toggle="tooltip" data-state="dark" title="Upload data anggota">
                    <i class="fa fa-upload"></i>
                    Upload
                </a>
            @endif
            @if (Auth::user()->isDev())
                <a href="{{ route('memberReset') }}" class="btn my-1 btn-sm btn-danger" data-toggle="tooltip" data-state="dark" title="Reset data anggota">
                    <i class="fa fa-recycle"></i>
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Data Anggota</div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Wilayah</th>
                            <th>Tanggal Bergabung</th>
                            <th>Telepon</th>
                            <th>Keanggotaan</th>
                            <th>Action</th>
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
                                <td>{{ $value->region->name ?? '' }}</td>
                                <td>{{ $value->join_date }}</td>
                                <td>{{ $value->phone }}</td>
                                <td>
                                    <span class="badge badge-{{ $value->status == 0 ? 'info' : ($value->status==1 ? 'success' : 'danger') }}">{{ $value->status == 0 ? 'Non Anggota' : ($value->status==1 ? 'Anggota' : 'Keluar') }}</span>
                                </td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('memberDetail'))
                                        <a href="{{ route('memberDetail', ['id' => $value->id]) }}" class="btn icon-btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Detail data">
                                            <i class="fa fa-user"></i>
                                        </a>
                                    @endif	
                                    @if ($value->status != 2)
                                        @if (Auth::user()->hasRule('memberEdit'))
                                            <a href="{{ route('memberEdit', ['id' => $value->id]) }}" class="btn icon-btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Edit data">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                        @endif
                                    @endif	
                                    @if ($value->status == 1)
                                        @if (Auth::user()->hasRule('memberDelete'))
                                            <a href="#" class="btn btn-sm icon-btn btn-danger data-delete" data-state="dark" data-toggle="tooltip" data-placement="top" data-url="{{ route('memberDelete', ['id'=>$value->id]) }}" title="Hapus data anggota" data-message="Anda yakin akan menghapus data anggota : {{$value->name}}?">
                                                <i class="fa fa-trash-alt"></i>
                                            </a>				
                                        @endif
                                    @endif
                                    @if ($value->status == 0)
                                        @if (Auth::user()->hasRule('memberPromotion'))
                                            <a href="{{ route('memberPromotion', ['id' => $value->id]) }}" class="btn icon-btn btn-dark btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Tambahkan jadi anggota">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        @endif
                                    @endif
                                    @if (Auth::user()->hasRule('memberTransaksi'))
                                        <a href="{{ route('memberTransaksi', ['id' => $value->id]) }}" class="btn icon-btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Data transaksi {{ $value->name }}">
                                            <i class="fa fa-bars"></i>
                                        </a>
                                    @endif
                                    @if (Auth::user()->hasRule('memberActivity'))
                                        <a href="{{ route('memberActivity', ['id' => $value->id]) }}" class="btn icon-btn btn-dark btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Catatan aktivitas {{ $value->name }}">
                                            <i class="fa fa-clipboard"></i>
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