@extends('layouts.application')

@section('module', 'Alokasi SHU')

@section('content')
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
            @if (Auth::user()->hasRule('shuConfigAdd'))
                <a href="{{ route('shuConfigAdd') }}" class="btn btn-info" data-toggle="tooltip" data-state="dark" title="Tambah alokasi shu">
                    <i class="fa fa-plus"></i>
                    Tambah
                </a>
            @endif
        </div>
    </form>
</div>
@if ($data['percent'] != 100)
	<div class="alert alert-dark-warning alert-dismissible fade show">
	    <i class="fa fa-exclamation-triangle"></i>
	    <strong>Warning!</strong> 
	    Persentase SHU harus berjumlah <b>100%</b>
	</div>
@endif
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Data Alokasi SHU</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Alokasi</th>
                            <th>Akun</th>
                            <th>Persentase</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                            @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $value->allocation }}</td>
                                <td>{{ $value->account }}</td>
                                <td>{{ number_format($value->percent, 2, ',', '.') }}%</td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('shuConfigEdit'))
                                        <a href="{{ route('shuConfigEdit', ['id' => $value->id]) }}" class="btn icon-btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Edit alokasi shu">
                                            <i class="fa fa-pen"></i>
                                        </a>
                                    @endif
                                    @if ($value->default == 0 && Auth::user()->hasRule('shuConfigDelete'))
                                        <a href="#" class="btn btn-sm icon-btn btn-danger data-delete" data-state="dark" data-toggle="tooltip" data-placement="top" data-url="{{ route('shuConfigDelete', ['id'=>$value->id]) }}" title="Hapus data alokasi shu" data-message="Anda yakin akan menghapus alokasi shu: {{ $value->allocation }}?">
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
                        Total Record : <strong>{{$data['data']->count()}}</strong>
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