@extends('layouts.application')

@section('module', 'Data User')

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
            <label class="form-label">Level</label>
            <select class="select2 form-control" name="level_id">
                <option value="all" value="{{$data['level_id']=='all'?'selected':''}}">--Pilih--</option>
                @foreach ($data['level'] as $value)
                    @if (!empty($value->name))
                        <option value="{{$value->id}}" {{$value->id==$data['level_id']?'selected':''}}>{{$value->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Urutkan</label>
            <select class="select2 form-control" name="sort_by">
                <option value="created_at" {{$data['sort_by'] == 'created_at'?'selected':''}}>Tanggal Input</option>
                <option value="username" {{$data['sort_by'] == 'username'?'selected':''}}>Username</option>
                <option value="name" {{$data['sort_by'] == 'name'?'selected':''}}>Nama</option>
                <option value="level_id" {{$data['sort_by'] == 'level_id'?'selected':''}}>Level</option>
                <option value="email" {{$data['sort_by'] == 'email'?'selected':''}}>Email</option>
                <option value="active" {{$data['sort_by'] == 'active'?'selected':''}}>Status</option>
            </select>
        </div>
        <div class="col-md-1 mb-3">
            <label class="form-label">&nbsp;</label>
            <select class="select2 form-control" name="order">
                <option value="asc" {{$data['order'] == 'asc'?'selected':''}}>Asc</option>
                <option value="desc" {{$data['order'] == 'desc'?'selected':''}}>Desc</option>
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
            @if (Auth::user()->hasRule('userAdd'))
                <a href="{{ route('userAdd') }}" class="btn btn-info" data-toggle="tooltip" data-state="dark" title="Tambah User Aplikasi">
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
            <div class="card-header h4 text-center">Data User</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Level</th>
                            <th>Status</th>
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
                                <td>{{ $value->username }}</td>
                                <td>{{ $value->email }}</td>
                                <td>{{ $value->level->name }}</td>
                                <td>
                                    <span class="badge badge-{{$value->active==1?'success':'danger'}} m-badge--wide">{{$value->active==1?'Aktif':'Tidak Aktif'}}</span>
                                </td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('userEdit'))
                                        <a href="{{ route('userEdit', ['id' => $value->id]) }}" class="btn icon-btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Edit User">
                                            <i class="fa fa-pen"></i>
                                        </a>
                                    @endif	
                                    @if (Auth::user()->hasRule('userDelete'))
                                        <a href="#" class="btn btn-sm icon-btn btn-danger data-delete" data-state="dark" data-toggle="tooltip" data-placement="top" data-url="{{ route('userDelete', ['id'=>$value->id]) }}" title="Hapus User" data-message="Anda yakin akan menghapus data user {{$value->name}}?">
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