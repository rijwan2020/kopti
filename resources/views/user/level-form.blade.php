@extends('layouts.application')
@section('module', 'Level User')

@section('content')

<div class="card">
    <div class="card-header h4 text-center">Form User</div>
    <form action="{{ route('levelSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">No Level *</label>
                        <input type="text" class="form-control {{ $errors->has('id')?' is-invalid':'' }}" placeholder="No Level" name="id" id="id" value="{{ old('id') ?? $data['data']->id ?? '' }}" required {{$data['mode']=='edit'?'readonly':''}}>
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Nomor Level.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Level *</label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Nama Level" name="name" id="name" value="{{ old('name') ?? $data['data']->name ?? '' }}" required>
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Nama Level.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Rules</label>
                        {{-- RULE DATA MASTER --}}
                        @foreach (config('rules.master') as $key => $value)
                            <div style="margin-left: {{$value['indent']*25}}px">
                                <input type="checkbox" value="1" name="rule[{{$key}}]" id="{{$key}}" data-parent="{{$value['parent']}}" {{ isset($data['data']->rule->$key) == 1?'checked':''}}> {{$value['label']}}
                            </div>
                        @endforeach
                        {{-- RULE SIMPANAN --}}
                        @foreach (config('rules.deposit') as $key => $value)
                            <div style="margin-left: {{$value['indent']*25}}px">
                                <input type="checkbox" value="1" name="rule[{{$key}}]" id="{{$key}}" data-parent="{{$value['parent']}}" {{ isset($data['data']->rule->$key) == 1?'checked':''}}> {{$value['label']}}
                            </div>
                        @endforeach
                        {{-- RULE TOKO --}}
                        @foreach (config('rules.store') as $key => $value)
                            <div style="margin-left: {{$value['indent']*25}}px">
                                <input type="checkbox" value="1" name="rule[{{$key}}]" id="{{$key}}" data-parent="{{$value['parent']}}" {{ isset($data['data']->rule->$key) == 1?'checked':''}}> {{$value['label']}}
                            </div>
                        @endforeach
                        {{-- RULE PEMBUKUAN --}}
                        @foreach (config('rules.accountancy') as $key => $value)
                            <div style="margin-left: {{$value['indent']*25}}px">
                                <input type="checkbox" value="1" name="rule[{{$key}}]" id="{{$key}}" data-parent="{{$value['parent']}}" {{ isset($data['data']->rule->$key) == 1?'checked':''}}> {{$value['label']}}
                            </div>
                        @endforeach
                        {{-- RULE LAPORAN --}}
                        @foreach (config('rules.report') as $key => $value)
                            <div style="margin-left: {{$value['indent']*25}}px">
                                <input type="checkbox" value="1" name="rule[{{$key}}]" id="{{$key}}" data-parent="{{$value['parent']}}" {{ isset($data['data']->rule->$key) == 1?'checked':''}}> {{$value['label']}}
                            </div>
                        @endforeach
                        {{-- RULE USER --}}
                        @foreach (config('rules.user') as $key => $value)
                            <div style="margin-left: {{$value['indent']*25}}px">
                                <input type="checkbox" value="1" name="rule[{{$key}}]" id="{{$key}}" data-parent="{{$value['parent']}}" {{ isset($data['data']->rule->$key) == 1?'checked':''}}> {{$value['label']}}
                            </div>
                        @endforeach
                        {{-- RULE RESET --}}
                        @foreach (config('rules.reset') as $key => $value)
                            <div style="margin-left: {{$value['indent']*25}}px">
                                <input type="checkbox" value="1" name="rule[{{$key}}]" id="{{$key}}" data-parent="{{$value['parent']}}" {{ isset($data['data']->rule->$key) == 1?'checked':''}}> {{$value['label']}}
                            </div>
                        @endforeach
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
    <script>
        $(document).ready(function() {
            $('input[type="checkbox"]').on('change', function(e){
                var el = $(this), parent;
                if (el.is(':not(:checked)')) {
                    $('input[data-parent="'+el.attr('id')+'"]').prop('checked', false).trigger('change');
                }else{
                    parent = el.attr('data-parent');
                    if (parent) {
                        $('#'+parent).prop("checked", true).trigger('change');
                    }
                }
            })
        });
    </script>
@endsection