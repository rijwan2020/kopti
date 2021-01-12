@extends('layouts.application')
@section('module', 'Data Karyawan')

@section('content')

<div class="card">
    <div class="card-header h4 text-center">Form Karyawan</div>
    <form action="{{ route('employeeSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}"> 
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">Nama Karyawan *</label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Nama Karyawan" name="name" id="name" value="{{ old('name') ?? $data['data']->name ?? '' }}" required>
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama karyawan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <div>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" value="0" {{ old('gender')==0 || (isset($data['data']->gender) AND $data['data']->gender==0) ? 'checked' : ''}}>
                                <span class="form-check-label">Perempuan</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" value="1" {{ old('gender')==1 || (isset($data['data']->gender) AND $data['data']->gender==1) ? 'checked' : ''}}>
                                <span class="form-check-label">Laki - Laki </span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" id="address" cols="30" rows="5" class="form-control {{ $errors->has('address')?' is-invalid':'' }}">{{ old('address') ?? $data['data']->address ?? '' }}</textarea>
                        {!! $errors->first('address', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Alamat karyawan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No Telepon</label>
                        <input type="text" class="form-control {{ $errors->has('phone')?' is-invalid':'' }}" placeholder="No Telepon" name="phone" id="phone" value="{{ old('phone') ?? $data['data']->phone ?? '' }}">
                        {!! $errors->first('phone', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan no telepon karyawan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control {{ $errors->has('email')?' is-invalid':'' }}" placeholder="Email" name="email" id="email" value="{{ old('email') ?? $data['data']->email ?? '' }}">
                        {!! $errors->first('email', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan email karyawan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pilih Posisi *</label>
                        <select class="form-control select2 {{ $errors->has('position_id')?' is-invalid':'' }}" name="position_id">
                            @foreach ($data['position'] as $value)
                                <option value="{{$value->id}}" {{$value->id==old('position_id')?'selected':''}}>{{$value->name}}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('position_id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih posisi karyawan.</small>
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