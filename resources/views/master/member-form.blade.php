@extends('layouts.application')
@section('module', 'Data Anggota')

@section('content')

<div class="card">
    <div class="card-header h4 text-center">Form Data Anggota</div>
    <form action="{{ route('memberSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    @if ($data['mode']=='add')
                        <div class="form-group">
                            <label class="form-label">Status Keanggotaan *</label>
                            <select class="form-control select2 {{ $errors->has('status')?' is-invalid':'' }}" name="status" onchange="changeCode(this.value)" required>
                                <option value=""></option>
                                <option value="0">Non Anggota</option>
                                <option value="1">Anggota</option>
                            </select>
                            {!! $errors->first('status', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Pilih status keanggotaan anggota.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kode Anggota *</label>
                            <input type="text" class="form-control {{ $errors->has('code')?' is-invalid':'' }}" placeholder="Kode Anggota" name="code" id="code" value="{{ old('code') ?? '' }}" readonly>
                        </div>
                    @else
                        <input type="hidden" name="id" value="{{$data['data']->id}}">
                        <input type="hidden" name="code" value="{{$data['data']->code}}">

                        <div class="form-group">
                            <label class="form-label">Kode Anggota</label>
                            <input type="text" class="form-control" value="{{ $data['data']->code ?? '' }}" disabled>
                            <small class="form-text text-muted">Kode anggota tidak dapat diperbaharui.</small>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">Nama Anggota *</label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Nama Anggota" name="name" id="name" value="{{ old('name') ?? $data['data']->name ?? '' }}" required>
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama anggota.</small>
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
                        <label class="form-label">Wilayah Anggota *</label>
                        <select class="form-control select2 {{ $errors->has('region_id')?' is-invalid':'' }}" name="region_id">
                            @foreach ($data['region'] as $key => $value)
                               <option value="{{$value->id}}" {{ isset($data['data']->region_id) && $data['data']->region_id == $value->id?'selected':'' }}>{{$value->name}}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('region_id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih wilayah anggota.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control {{ $errors->has('place_of_birth')?' is-invalid':'' }}" placeholder="Tempat Lahir" name="place_of_birth" id="place_of_birth" value="{{ old('place_of_birth') ?? $data['data']->place_of_birth ?? '' }}">
                        {!! $errors->first('place_of_birth', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan tempat lahir anggota.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir</label>
                        <div class="input-group" style="width: 40%">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('date_of_birth')?' is-invalid':'' }} datepicker" placeholder="Tanggal Lahir" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') ?? $data['data']['date_of_birth'] ?? '' }}">
                        </div>
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Tanggal Lahir anggota.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Agama</label>
                        <input type="text" class="form-control {{ $errors->has('religion')?' is-invalid':'' }}" placeholder="Agama" name="religion" id="religion" value="{{ old('religion') ?? $data['data']->religion ?? '' }}">
                        {!! $errors->first('religion', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan agama anggota.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pendidikan</label>
                        <select class="form-control select2 {{ $errors->has('education')?' is-invalid':'' }}" name="education">
                            <option value=""></option>
                            <option {{ isset($data['data']->education) == "TK" ? 'selected':''}} value="TK">TK</option>
                            <option {{ isset($data['data']->education) == "SD/Sederajat" ? 'selected':''}} value="SD/Sederajat">SD/Sederajat</option>
                            <option {{ isset($data['data']->education) == "SMP/Sederajat" ? 'selected':''}} value="SMP/Sederajat">SMP/Sederajat</option>
                            <option {{ isset($data['data']->education) == "SMA/Sederajat" ? 'selected':''}} value="SMA/Sederajat">SMA/Sederajat</option>
                            <option {{ isset($data['data']->education) == "D1" ? 'selected':''}} value="D1">D1</option>
                            <option {{ isset($data['data']->education) == "D2" ? 'selected':''}} value="D2">D2</option>
                            <option {{ isset($data['data']->education) == "D3" ? 'selected':''}} value="D3">D3</option>
                            <option {{ isset($data['data']->education) == "D4" ? 'selected':''}} value="D4">D4</option>
                            <option {{ isset($data['data']->education) == "S1" ? 'selected':''}} value="S1">S1</option>
                            <option {{ isset($data['data']->education) == "S2" ? 'selected':''}} value="S2">S2</option>
                            <option {{ isset($data['data']->education) == "S3" ? 'selected':''}} value="S3">S3</option>
                            <option {{ isset($data['data']->education) == "Lainnya" ? 'selected':''}} value="Lainnya">Lainnya</option>
                        </select>
                        {!! $errors->first('education', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih pendidikan anggota.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" id="address" cols="30" rows="5" class="form-control {{ $errors->has('address')?' is-invalid':'' }}">{{ old('address') ?? $data['data']->address ?? '' }}</textarea>
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Alamat anggota.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Kelurahan / Desa *</label>
                        <select name="village_id" class="form-control desa {{ $errors->has('village_id')?' has-danger':'' }}">
                            @if (isset($data['data']->village_id))
                                <option value="{{$data['data']->village_id}}" selected>{{$data['data']->village->name}}, {{$data['data']->district->name}}, {{$data['data']->regency->name}}, {{$data['data']->province->name}}</option>
                            @endif
                        </select>
                        <small class="form-text text-muted">Ketikan alamat desa dari anggota.</small>
                        {!! $errors->first('village_id', '<small class="text-danger">:message</small>') !!}
                    </div>

                    <div class="form-group">
                        <label class="form-label">No Telepon</label>
                        <input type="text" class="form-control {{ $errors->has('phone')?' is-invalid':'' }}" placeholder="No Telepon" name="phone" id="phone" value="{{ old('phone') ?? $data['data']->phone ?? '' }}">
                        {!! $errors->first('phone', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan no telepon anggota.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pengrajin</label>
                        <input type="text" class="form-control {{ $errors->has('craftman')?' is-invalid':'' }}" placeholder="Pengrajin" name="craftman" id="craftman" value="{{ old('craftman') ?? $data['data']->craftman ?? '' }}">
                        {!! $errors->first('craftman', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Pengrajin anggota (tahu/tempe).</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jatah Kedelai</label>
                        <div class="input-group" style="width: 30%">
                            <input type="text" class="form-control {{ $errors->has('soybean_ration')?' is-invalid':'' }}" placeholder="Jatah Kedelai" name="soybean_ration" id="soybean_ration" value="{{ old('soybean_ration') ?? $data['data']->soybean_ration ?? 0 }}" >
                            <div class="input-group-prepend">
                                <span class="input-group-text">Kg</span>
                            </div>
                        </div>
                        {!! $errors->first('soybean_ration', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan jatah kedelai anggota.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bahan Baku</label>
                        <input type="text" class="form-control {{ $errors->has('raw_material')?' is-invalid':'' }}" placeholder="Bahan Baku" name="raw_material" id="raw_material" value="{{ old('raw_material') ?? $data['data']->raw_material ?? '' }}">
                        {!! $errors->first('raw_material', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan bahan baku produksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bahan Pembantu</label>
                        <input type="text" class="form-control {{ $errors->has('adjuvant')?' is-invalid':'' }}" placeholder="Bahan Pembantu" name="adjuvant" id="adjuvant" value="{{ old('adjuvant') ?? $data['data']->adjuvant ?? '' }}">
                        {!! $errors->first('adjuvant', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan bahan pembantu produksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bahan Tambahan</label>
                        <input type="text" class="form-control {{ $errors->has('extra_material')?' is-invalid':'' }}" placeholder="Bahan Tambahan" name="extra_material" id="extra_material" value="{{ old('extra_material') ?? $data['data']->extra_material ?? '' }}">
                        {!! $errors->first('extra_material', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan bahan tambahan produksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hasil Produksi</label>
                        <input type="text" class="form-control {{ $errors->has('production_result')?' is-invalid':'' }}" placeholder="Hasil Produksi" name="production_result" id="production_result" value="{{ old('production_result') ?? $data['data']->production_result ?? '' }}">
                        {!! $errors->first('production_result', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Hasil Produksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pendapatan</label>
                        <div class="input-group" style="width: 30%">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('income')?' is-invalid':'' }} money-without-separator" placeholder="Pendapatan" name="income" id="income" value="{{ old('income') ?? number_format($data['data']->income ?? 0) }}">
                        </div>
                        {!! $errors->first('income', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Pendapatan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pemasaran</label>
                        <input type="text" class="form-control {{ $errors->has('marketing')?' is-invalid':'' }}" placeholder="Pemasaran" name="marketing" id="marketing" value="{{ old('marketing') ?? $data['data']->marketing ?? '' }}">
                        {!! $errors->first('marketing', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan pemasaran produksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Permodalan</label>
                        <input type="text" class="form-control {{ $errors->has('capital')?' is-invalid':'' }}" placeholder="Permodalan" name="capital" id="capital" value="{{ old('capital') ?? $data['data']->capital ?? '' }}">
                        {!! $errors->first('capital', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Permodalan produksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pengalaman</label>
                        <input type="text" class="form-control {{ $errors->has('experience')?' is-invalid':'' }}" placeholder="Pengalaman" name="experience" id="experience" value="{{ old('experience') ?? $data['data']->experience ?? '' }}">
                        {!! $errors->first('experience', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Pengalaman produksi.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Domisili</label>
                        <textarea name="domicile" id="domicile" cols="30" rows="5" class="form-control {{ $errors->has('domicile')?' is-invalid':'' }}">{{ old('domicile') ?? $data['data']->domicile ?? '' }}</textarea>
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Domisili anggota.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tempat Usaha</label>
                        <input type="text" class="form-control {{ $errors->has('place_of_business')?' is-invalid':'' }}" placeholder="Tempat Usaha" name="place_of_business" id="place_of_business" value="{{ old('place_of_business') ?? $data['data']->place_of_business ?? '' }}">
                        {!! $errors->first('place_of_business', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Tempat Usaha produksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alat Produksi</label>
                        <input type="text" class="form-control {{ $errors->has('production_tool')?' is-invalid':'' }}" placeholder="Alat Produksi" name="production_tool" id="production_tool" value="{{ old('production_tool') ?? $data['data']->production_tool ?? '' }}">
                        {!! $errors->first('production_tool', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Alat Produksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kriteria</label>
                        <input type="text" class="form-control {{ $errors->has('criteria')?' is-invalid':'' }}" placeholder="Kriteria" name="criteria" id="criteria" value="{{ old('criteria') ?? $data['data']->criteria ?? '' }}">
                        {!! $errors->first('criteria', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Kriteria alat produksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Surat HO</label>
                        <div>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="ho_letter" value="0" {{ old('ho_letter')==0 || (isset($data['data']->ho_letter) AND $data['data']->ho_letter==0) ? 'checked' : ''}}>
                                <span class="form-check-label">Tidak Ada</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="ho_letter" value="1" {{ old('ho_letter')==1 || (isset($data['data']->ho_letter) AND $data['data']->ho_letter==1) ? 'checked' : ''}}>
                                <span class="form-check-label">Ada</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Surat Izin</label>
                        <div>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="license" value="0" {{ old('license')==0 || (isset($data['data']->license) AND $data['data']->license==0) ? 'checked' : ''}}>
                                <span class="form-check-label">Tidak Ada</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="license" value="1" {{ old('license')==1 || (isset($data['data']->license) AND $data['data']->license==1) ? 'checked' : ''}}>
                                <span class="form-check-label">Ada</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Surat IMB</label>
                        <div>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="imb_letter" value="0" {{ old('imb_letter')==0 || (isset($data['data']->imb_letter) AND $data['data']->imb_letter==0) ? 'checked' : ''}}>
                                <span class="form-check-label">Tidak Ada</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="imb_letter" value="1" {{ old('imb_letter')==1 || (isset($data['data']->imb_letter) AND $data['data']->imb_letter==1) ? 'checked' : ''}}>
                                <span class="form-check-label">Ada</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Surat PBB</label>
                        <div>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pbb_letter" value="0" {{ old('pbb_letter')==0 || (isset($data['data']->pbb_letter) AND $data['data']->pbb_letter==0) ? 'checked' : ''}}>
                                <span class="form-check-label">Tidak Ada</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pbb_letter" value="1" {{ old('pbb_letter')==1 || (isset($data['data']->pbb_letter) AND $data['data']->pbb_letter==1) ? 'checked' : ''}}>
                                <span class="form-check-label">Ada</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alat Pemadam</label>
                        <div>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="extinguisher" value="0" {{ old('extinguisher')==0 || (isset($data['data']->extinguisher) AND $data['data']->extinguisher==0) ? 'checked' : ''}}>
                                <span class="form-check-label">Tidak Ada</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="extinguisher" value="1" {{ old('extinguisher')==1 || (isset($data['data']->extinguisher) AND $data['data']->extinguisher==1) ? 'checked' : ''}}>
                                <span class="form-check-label">Ada</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Bergabung *</label>
                        <div class="input-group" style="width: 40%">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('join_date')?' is-invalid':'' }} datepicker" placeholder="Tanggal Bergabung" name="join_date" id="join_date" value="{{ old('join_date') ?? $data['data']->join_date ?? '' }}" required>
                        </div>
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Tanggal Bergabung anggota.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tanggungan</label>
                        <textarea name="dependent" id="dependent" cols="30" rows="5" class="form-control {{ $errors->has('dependent')?' is-invalid':'' }}">{{ old('dependent') ?? $data['data']->dependent ?? '' }}</textarea>
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Tanggungan anggota.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah Tanggungan</label>
                        <div class="input-group" style="width: 30%">
                            <input type="text" class="form-control {{ $errors->has('total_dependent')?' is-invalid':'' }}" placeholder="Jumlah Tanggungan" name="total_dependent" id="total_dependent" value="{{ old('total_dependent') ?? number_format($data['data']->total_dependent ?? 0) }}">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Jiwa</span>
                            </div>
                        </div>
                        {!! $errors->first('total_dependent', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Jumlah Tanggungan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah Anak</label>
                        <div class="input-group" style="width: 30%">
                            <input type="text" class="form-control {{ $errors->has('total_children')?' is-invalid':'' }}" placeholder="Jumlah Anak" name="total_children" id="total_children" value="{{ old('total_children') ?? number_format($data['data']->total_children ?? 0) }}">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Jiwa</span>
                            </div>
                        </div>
                        {!! $errors->first('total_children', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Jumlah Anak.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Foto</label>
                        <div class="input-group file-input">
                            <label class="custom-file">
                                <input type="file" class="custom-file-input upload {{ $errors->has('image')?' is-invalid':'' }}" data-target="input-image" name="image" placeholder="No File Selected">
                                <span class="custom-file-label" id="input-image"></span>
                            </label>
                        </div>
                        {!! $errors->first('image', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">File harus berformat .jpg / .jpeg / .png</small>
                    </div>

                    @if ($data['mode'] == 'edit' && $data['data']->status == 1)
                        <div class="form-group">
                            <label class="form-label">Username *</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-user-edit"></i></span>
                                </div>
                                <input type="text" class="form-control {{ $errors->has('username')?' is-invalid':'' }}" placeholder="Username" name="username" id="username" value="{{ old('username') ?? $data['data']->user->username ?? '' }}" required>
                            </div>
                            {!! $errors->first('username', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Masukan username untuk digunakan login</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-key"></i></span>
                                </div>
                                <input type="password" class="form-control {{ $errors->has('password')?' is-invalid':'' }}" placeholder="Password" name="password" id="password" value="{{ old('password') ?? '' }}">
                                <div style="position: absolute; right: 5px; top: 25%; transform: translate(-50%,0); cursor: pointer;" id="togglePass">
                                    <i class="fa fa-eye" id="icon-pass"></i>
                                </div>
                            </div>
                            {!! $errors->first('password', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">
                                Password minimal 6 karakter. Kosongkan password jika tidak akan mengubah password.
                            </small>
                        </div>
                    @endif

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
        function pad (str, max) {
            str = str.toString();
            return str.length < max ? pad("0" + str, max) : str;
        }
        function changeCode(id) {
            if (id == 1) {
                $kode = 'A-'+pad({{config('config_apps.next_code_anggota')}}, 4);
            }else{
                $kode = 'N-'+pad({{config('config_apps.next_code_non_anggota')}}, 4);
            }
            document.getElementById('code').value = $kode;
        }
    </script>
    
@endsection