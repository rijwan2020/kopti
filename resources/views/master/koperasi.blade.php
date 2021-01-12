@extends('layouts.application')
@section('module', 'Profile Koperasi')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Profile Koperasi</div>
    <form action="{{ route('koperasiUpdate') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group text-center">
                        <a href="{{ !isset($data['data']['logo']) || $data['data']['logo']==''?asset('storage/logo.png'):asset('storage/'.$data['data']['logo']) }}" target="_blank">
                            <img src="{{ !isset($data['data']['logo']) || $data['data']['logo']==''?asset('storage/logo.png'):asset('storage/'.$data['data']['logo']) }}" alt="" class="ui-w-120">
                        </a>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Logo</label>
                        <div class="input-group file-input">
                            <label class="custom-file">
                                <input type="file" class="custom-file-input upload {{ $errors->has('logo')?' is-invalid':'' }}" data-target="input-foto" name="logo" placeholder="No File Selected">
                                <span class="custom-file-label" id="input-foto"></span>
                            </label>
                        </div>
                        {!! $errors->first('logo', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">File harus berformat .jpg / .jpeg / .png</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Koperasi *</label>
                        <input type="text" class="form-control {{ $errors->has('nama')?' is-invalid':'' }}" placeholder="Nama Koperasi" name="nama" id="nama" value="{{ old('nama') ?? $data['data']['nama'] }}" required>
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama koperasi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi </label>
                        <textarea name="deskripsi" id="deskripsi" cols="30" rows="5" class="form-control {{ $errors->has('deskripsi')?' is-invalid':'' }}">{{ old('deskripsi') ?? $data['data']['deskripsi'] }}</textarea>
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan deskripsi koperasi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat </label>
                        <textarea name="alamat" id="alamat" cols="30" rows="5" class="form-control {{ $errors->has('alamat')?' is-invalid':'' }}">{{ old('alamat') ?? $data['data']['alamat'] }}</textarea>
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan alamat koperasi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kelurahan / Desa *</label>
                        <select name="village_id" class="form-control desa {{ $errors->has('village_id')?' has-danger':'' }}">
                            @if (isset($data['data']['village_id']))
                                <option value="{{$data['data']['village_id']}}" selected>{{$data['village']->name}}, {{$data['village']->district->name}}, {{$data['village']->regency->name}}, {{$data['village']->province->name}}</option>
                            @endif
                        </select>
                        <small class="form-text text-muted">Ketikan alamat desa dari koperasi.</small>
                        {!! $errors->first('village_id', '<small class="text-danger">:message</small>') !!}
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control {{ $errors->has('email')?' is-invalid':'' }}" placeholder="Email Koperasi" name="email" id="email" value="{{ old('email') ?? $data['data']['email'] }}">
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan email koperasi.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">No Telepon</label>
                        <input type="text" class="form-control {{ $errors->has('no_telepon')?' is-invalid':'' }}" placeholder="No Telepon Koperasi" name="no_telepon" id="no_telepon" value="{{ old('no_telepon') ?? $data['data']['no_telepon'] }}">
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan No Telepon koperasi.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Bentuk Koperasi</label>
                        <select class="form-control select2 {{ $errors->has('bentuk_id')?' is-invalid':'' }}" name="bentuk_id">
                            <option value="0" {{config('koperasi.bentuk_id')==0?'selected':''}}>--Pilih--</option>
                            @foreach (config('data.bentuk') as $key => $value)
                               <option value="{{$key}}" {{config('koperasi.bentuk_id') == $key?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('bentuk_id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih bentuk koperasi.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Jenis Koperasi</label>
                        <select class="form-control select2 {{ $errors->has('jenis_id')?' is-invalid':'' }}" name="jenis_id">
                            <option value="0" {{config('koperasi.jenis_id')==0?'selected':''}}>--Pilih--</option>
                            @foreach (config('data.jenis') as $key => $value)
                               <option value="{{$key}}" {{config('koperasi.jenis_id') == $key?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('jenis_id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih bentuk koperasi.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Website</label>
                        <input type="text" class="form-control {{ $errors->has('website')?' is-invalid':'' }}" placeholder="Website Koperasi" name="website" id="website" value="{{ old('website') ?? $data['data']['website'] }}">
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Website koperasi.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">No Badan Hukum</label>
                        <input type="text" class="form-control {{ $errors->has('no_badan_hukum')?' is-invalid':'' }}" placeholder="No Badan Hukum Koperasi" name="no_badan_hukum" id="no_badan_hukum" value="{{ old('no_badan_hukum') ?? $data['data']['no_badan_hukum'] }}">
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan No Badan Hukum koperasi.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tanggal Badan Hukum</label>
                        <input type="text" class="form-control {{ $errors->has('tanggal_badan_hukum')?' is-invalid':'' }} datepicker" placeholder="Tanggal Badan Hukum Koperasi" name="tanggal_badan_hukum" id="tanggal_badan_hukum" value="{{ old('tanggal_badan_hukum') ?? $data['data']['tanggal_badan_hukum'] }}">
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Tanggal Badan Hukum koperasi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><b>Catatan :</b> Field yang diberi tanda bintang (*) <b>harus diisi.</b></label>
                    </div>

                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Update">Update</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/toggle-pass.js') }}"></script>
    <script src="{{ asset('js/file-upload.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.desa').select2({
                placeholder: 'Pilih Kelurahan/Desa',
                ajax: {
                    url: '/desa',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results:  $.map(data, function (item) {
                                return {
                                    text: item.name+', '+item.district_name+', '+item.regency_name+', '+item.province_name,
                                    id: item.id
                                }
                            })
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endsection