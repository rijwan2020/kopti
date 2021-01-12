@extends('layouts.application')

@section('module', 'Data Akun')

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
            @if (Auth::user()->hasRule('accountAdd'))
                <a href="{{ route('accountAdd') }}" class="btn my-1 btn-primary" data-toggle="tooltip" data-state="dark" title="Tambah data akun">
                    <i class="fa fa-plus"></i>
                    Tambah
                </a>
            @endif
            @if (Auth::user()->hasRule('accountConfig'))
                <a href="{{ route('accountConfig') }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Konfigurasi saldo awal">
                    <i class="fa fa-dollar-sign"></i>
                    Set Saldo
                </a>
            @endif
            @if (Auth::user()->hasRule('accountGroupList'))
                <a href="{{ route('accountGroupList') }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Kelompok akun">
                    <i class="fa fa-bars"></i>
                    Kelompok Akun
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Data Akun</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th>Kelompok Akun</th>
                            <th>Saldo Normal</th>
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
                                <td style="padding-left: {{($value->level-1)*25}}px;">{{ $value->code }}</td>
                                <td>{{ $value->name }}</td>
                                <td>{{ $value->group->name ?? '' }}</td>
                                <td><span class="badge badge-{{ $value->type == 1 ? 'info' : 'success' }}">{{ $value->type == 1 ? 'Kredit' : 'Debit' }}</span></td>
                                <td class="text-center">
                                    @if ($value->level == 3)
                                        @if (Auth::user()->hasRule('accountEdit'))
                                            <a href="{{ route('accountEdit', ['id' => $value->id]) }}" class="btn icon-btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Edit data akun">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                        @endif	
                                        @if (Auth::user()->hasRule('accountDelete') && $value->default==0)
                                            <a href="#" class="btn btn-sm icon-btn btn-danger data-delete" data-state="dark" data-toggle="tooltip" data-placement="top" data-url="{{ route('accountDelete', ['id'=>$value->id]) }}" title="Hapus data akun" data-message="Anda yakin akan menghapus data akun : {{ $value->name }}?">
                                                <i class="fa fa-trash-alt"></i>
                                            </a>				
                                        @endif
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