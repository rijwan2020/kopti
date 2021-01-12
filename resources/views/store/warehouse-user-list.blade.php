@extends('layouts.application')

@section('module', 'Data User Gudang')

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
            <label class="form-label">Gudang</label>
            <select class="select2 form-control" name="wh_id">
                <option value="all" {{ $data['wh_id'] == 'all' ? 'selected' : '' }}>--Semua--</option>
                @foreach ($data['gudang'] as $value)
                    <option value="{{ $value->id }}" {{ $data['wh_id'] == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
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
            @if (Auth::user()->hasRule('warehouseUserAdd'))
                <a href="{{ route('warehouseUserAdd') }}" class="btn btn-info" data-toggle="tooltip" data-state="dark" title="Tambah data user gudang">
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
            <div class="card-header h4 text-center">Data User Gudang</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Gudang</th>
                            <th>Username</th>
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
                                <td>{{ $value->user->name ?? '' }}</td>
                                <td>{{ $value->user->email ?? '' }}</td>
                                <td>{{ $value->warehouse->name ?? '' }}</td>
                                <td>{{ $value->user->username ?? '' }}</td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('warehouseUserEdit'))
                                        <a href="{{ route('warehouseUserEdit', ['id' => $value->id]) }}" class="btn icon-btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Edit user gudang">
                                            <i class="fa fa-pen"></i>
                                        </a>
                                    @endif	
                                    @if (Auth::user()->hasRule('warehouseUserDelete'))
                                        <a href="#" class="btn btn-sm icon-btn btn-danger data-delete" data-state="dark" data-toggle="tooltip" data-placement="top" data-url="{{ route('warehouseUserDelete', ['id'=>$value->id]) }}" title="Hapus user gudang" data-message="Anda yakin akan menghapus data user {{ $value->user->name ?? '' }}?">
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