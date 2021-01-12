@extends('layouts.application')

@section('module', 'Tutup Buku')
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
            @if (Auth::user()->hasRule('closeMonthlyBookAdd'))
                <a href="{{ route('closeMonthlyBookAdd') }}" class="btn btn-info mb-3" data-toggle="tooltip" data-state="dark" data-placement="bottom" data-original-title="Tambah Data Tutup Buku bulanan">
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
            <div class="card-header text-center h4">
                Data Tutup Buku Bulanan
            </div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Tanggal Tutup Buku</th>
                            <th>Ditutup Oleh</th>
                            <th>Awal Periode</th>
                            <th>Akhir Periode</th>
                            <th>Catatan</th>
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
                                <td class="text-center">{{ $i }}</td>
                                <td>{{ $value->closing_date }}</td>
                                <td>{{ $value->userInput->name ?? '' }}</td>
                                <td>{{ $value->start_periode }}</td>
                                <td>{{ $value->end_periode }}</td>
                                <td>{{ $value->description }}</td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('closeMonthlyBookDetail'))
                                        <a href="{{ route('closeMonthlyBookDetail', ['id' => $value->id]) }}" class="btn icon-btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Detail Tutup Buku">
                                            <i class="fa fa-bars"></i>
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