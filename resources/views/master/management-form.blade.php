@extends('layouts.application')
@section('module', 'Data Pengurus')

@section('content')

<div class="card">
    <div class="card-header h4 text-center">Form Pengurus</div>
    <form action="{{ route('managementSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}"> 
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    @if ($data['mode']=='add')
                        <div class="form-group">
                            <label class="form-label">Pilih Anggota *</label>
                            <select class="form-control select2 {{ $errors->has('member_id')?' is-invalid':'' }}" name="member_id">
                                @foreach ($data['member'] as $value)
                                    <option value="{{$value->id}}" {{$value->id==old('member_id')?'selected':''}}>[{{$value->code}}] - {{$value->name}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('member_id', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Pilih anggota untuk dijadikan pengurus.</small>
                        </div>
                    @else
                        <div class="form-group">
                            <label class="form-label">Nama Anggota</label>
                            <input type="text" class="form-control" value="[{{ $data['data']->member->code }}] - {{ $data['data']->member->name }}" disabled>
                        </div>
                        <input type="hidden" name="member_id" value="{{$data['data']->member_id}}">
                    @endif

                    <div class="form-group">
                        <label class="form-label">Pilih Jabatan *</label>
                        <select class="form-control select2 {{ $errors->has('position_id')?' is-invalid':'' }}" name="position_id">
                            @foreach ($data['position'] as $value)
                                <option value="{{$value->id}}" {{$value->id==old('position_id')?'selected':''}}>{{$value->name}}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('position_id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih jabatan kepengurusan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('username')?' is-invalid':'' }}" placeholder="Username" name="username" id="username" value="{{ old('username') ?? $data['data']->user->username ?? '' }}" required>
                        </div>
                        {!! $errors->first('username', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan username untuk digunakan login.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password @if ($data['mode'] == 'add')*@endif</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            </div>
                            <input type="password" class="form-control {{ $errors->has('password')?' is-invalid':'' }}" placeholder="Password" name="password" id="password" value="{{ old('password') ?? '' }}" {{$data['mode']=='add'?'required':''}}>
                            <div style="position: absolute; right: 5px; top: 25%; transform: translate(-50%,0); cursor: pointer;" id="togglePass">
                                <i class="fa fa-eye" id="icon-pass"></i>
                            </div>
                        </div>
                        {!! $errors->first('password', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">
                            Password minimal 6 karakter. 
                             @if ($data['mode']=='edit')
                                Kosongkan password jika tidak akan mengubah password.
                            @endif
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><b>Catatan :</b> Field yang diberi tanda bintang (*) <b>harus diisi.</b></label>
                    </div>

                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Save</button>
        </div>
    </form>
</div>
@endsection
@section('scripts')
    <script src="{{ asset('js/toggle-pass.js') }}"></script>
@endsection