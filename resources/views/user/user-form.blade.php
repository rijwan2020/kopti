@extends('layouts.application')
@section('module', 'Data User')

@section('content')

<div class="card">
    <div class="card-header h4 text-center">Form User</div>
    <form action="{{ route('userSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}">   
                <input type="hidden" name="image_old" value="{{$data['data']->image}}">   
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">Nama *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Nama" name="name" id="name" value="{{ old('name') ?? @$data['data']->name }}" required>
                        </div>
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama untuk user.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('username')?' is-invalid':'' }}" placeholder="Username" name="username" id="username" value="{{ old('username') ?? @$data['data']->username }}" required>
                        </div>
                        {!! $errors->first('username', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan username untuk digunakan login</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                            </div>
                            <input type="email" class="form-control {{ $errors->has('email')?' is-invalid':'' }}" placeholder="user@gmail.com" name="email" id="email" value="{{ old('email') ?? @$data['data']->email }}">
                        </div>
                        {!! $errors->first('email', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan email dengan format seperti contoh. Contoh <b>user@gmail.com</b></small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password *</label>
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

                    @if ($data['mode']=='add')
                        <div class="form-group">
                            <label class="form-label">Level *</label>
                            <select class="form-control select2 {{ $errors->has('level')?' is-invalid':'' }}" name="level_id">
                                @foreach ($data['level'] as $value)
                                    @if (!empty($value->name))
                                        <option value="{{$value->id}}" {{$value->id==@$data['level_id']?'selected':''}}>{{$value->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            {!! $errors->first('level', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Pilih level user</small>
                        </div>
                    @else
                        <input type="hidden" name="level_id" value="{{$data['data']->level_id}}">
                    @endif

                    <div class="form-group">
                        <label class="form-label">Foto</label>
                        <div class="input-group file-input">
                            <label class="custom-file">
                                <input type="file" class="custom-file-input upload {{ $errors->has('image')?' is-invalid':'' }}" data-target="input-foto" name="image">
                                <span class="custom-file-label" id="input-foto"></span>
                            </label>
                        </div>
                        {!! $errors->first('image', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">File harus berformat .jpg / .jpeg / .png</small>
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
    <script src="{{ asset('js/file-upload.js') }}"></script>
@endsection