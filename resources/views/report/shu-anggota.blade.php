@extends('layouts.application')

@section('module', 'SHU Anggota')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        @if (isset($data['tbb_id']))
            <input type="hidden" name="tbb_id" value="{{ $data['tbb_id'] }}">
        @endif
        @if (isset($data['tbt_id']))
            <input type="hidden" name="tbt_id" value="{{ $data['tbt_id'] }}">
        @endif
        <input type="hidden" name="start_date" value="{{ $data['start_date'] }}">
        <input type="hidden" name="end_date" value="{{ $data['end_date'] }}">
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
            @if (Auth::user()->hasRule('shuAnggotaPrint'))
                <a href="{{ route('shuAnggotaPrint', $data['param']) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print SHU Anggota" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('shuAnggotaDownload'))
                <a href="{{ route('shuAnggotaDownload', $data['param']) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download SHU Anggota">
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
            <div class="card-header h4 text-center">Data Sisa Hasil Usaha Anggota</div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Kode Anggota</th>
                            <th>Nama Anggota</th>
                            <th>Wilayah</th>
                            <th>Status</th>
                            <th>SHU Simpanan (Rp)</th>
                            <th>SHU Toko (Rp)</th>
                            <th>Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = ($data['data']->currentPage() - 1) * $data['data']->perPage();
                            $status = [
                                0 => 'Non Anggota',
                                1 => 'Anggota Aktif',
                                2 => 'Anggota Keluar'
                            ];
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $i }}</td>
                                <td>{{ $value->code }}</td>
                                <td>{{ $value->name }}</td>
                                <td>{{ $value->region->name }}</td>
                                <td>{{ $status[$value->status] }}</td>
                                <td>{{ number_format($value->shu_simpanan, 2, ',', '.') }}</td>
                                <td>{{ number_format($value->shu_toko, 2, ',', '.') }}</td>
                                <td>{{ number_format($value->shu_toko + $value->shu_simpanan, 2, ',', '.') }}</td>
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