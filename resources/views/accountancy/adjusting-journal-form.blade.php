@extends('layouts.application')

@section('module', 'Jurnal Penyesuaian')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Jurnal Penyesuaian</div>
    <form action="{{ route('adjustingJournalSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="input-jurnal-form">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}">   
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">
                    <div class="form-group">
                        <label class="form-label">Tanggal Transaksi *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                            <input type="text" class="form-control datepicker {{ $errors->has('transaction_date')?' is-invalid':'' }}" placeholder="Select Date" name="transaction_date" value="{{ old('transaction_date') ?? date('Y-m-d', strtotime($data['data']['transaction_date'])) }}">
                        </div>
                        {!! $errors->first('transaction_date', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan tanggal transaksi. Gunakan Format <b>Tahun-Bulan-Hari</b>.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. Ref / No. Bukti *</label>
                         <input type="text" class="form-control {{ $errors->has('reference_number')?' is-invalid':'' }} reference_number" name="reference_number" id="reference_number" value="{{ old('reference_number') ?? $data['data']['reference_number'] }}">
                        {!! $errors->first('reference_number', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan No referensi atau no bukti transaksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan Transaksi *</label>
                         <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }} name" name="name" id="name" value="{{ old('name') ?? @$data['data']['name'] }}">
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan keterangan transaksi.</small>
                    </div>

                    @if ($data['mode'] == 'add')
                        <div class="form-group">
                            <label class="form-label">Tipe Transaksi *</label>
                            <div>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" value="0" {{ old('type')==0 || (isset($data['data']->type) AND $data['data']->type==0) ? 'checked' : ''}} onclick="pemasukan()">
                                    <span class="form-check-label">Pemasukan</span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" value="1" {{ old('type')==1 || (isset($data['data']->type) AND $data['data']->type==1) ? 'checked' : ''}} onclick="pengeluaran()">
                                    <span class="form-check-label">Pengeluaran</span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" value="2" {{ old('type')==2 || (isset($data['data']->type) AND $data['data']->type==2) ? 'checked' : ''}} onclick="penyesuaian()">
                                    <span class="form-check-label">Penyesuaian</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label ">Rincian *</label>
                            <div>
                                <label class="form-label" id="label-debit"><b>Dana dimasukan ke <i>(Debit)</i> :</b></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Kodeakun</span>
                                    </div>
                                    <select class="form-control sumberdana-input select2" name="top_account[1]" id_item="1" style="width: 50%;">
                                        @foreach ($data['account'] as $value)
                                           <option value="{{$value->code}}" {{old('top_account[1]')==$value->code?'selected':''}} >{{'['.$value->code.'] - '.$value->name}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" class="type-input" id_item="1" name="top_type[1]" value="dana_from"/>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control nominal-input-atas money-with-separator" name="top_amount[1]" id_item="1" value="{{ old('top_amount[1]') ?? 0 }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-times"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div id="newRowsAtas"></div><!-- newRows -->

                            <div class="text-right mt-2">
                                <a href="#!" class="btn btn-primary" id="topAdd" data-toggle="tooltip" data-state="dark" data-placement="right" title="Tambah kodeakun yang berkaitan dengan transaksi">
                                    <span class="fa fa-plus"></span> Tambah
                                </a>
                            </div>

                            <hr>

                            <div>
                                <label class="form-label" id="label-kredit"><b>Dana didapatkan dari <i>(Kredit)</i> :</b></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Kodeakun</span>
                                    </div>
                                    <select class="form-control tujuandana-input select2" name="bottom_account[1]" id_item="1" required>
                                        @foreach ($data['account'] as $value)
                                            <option value="{{$value->code}}" {{old('bottom_account[1]')==$value->code?'selected':''}}>{{'['.$value->code.'] - '.$value->name}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" class="type-input" id_item="1" name="bottom_type[1]" value="dana_to"/>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control nominal-input-bawah money-with-separator" name="bottom_amount[1]" min="0" id_item="1" value="{{ old('bottom_amount[1]') ?? 0 }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-times"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div id="newRowsBawah"></div><!-- newRows -->

                            <div class="text-right mt-2">
                                <a href="#!" class="btn btn-primary" id="bottomAdd" data-toggle="tooltip" data-state="dark" data-placement="right" title="Tambah kodeakun yang berkaitan dengan transaksi">
                                    <span class="fa fa-plus"></span> Tambah
                                </a>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="type" value="{{$data['data']->type}}">
                        <div class="form-group">
                            <label class="form-label ">Tipe Transaksi</label>
                            <div>
                                <input type="text" class="form-control" disabled value="{{ $data['data']->type==0?'Pemasukan':($data['data']->type==1?'Pengeluaran':'Penyesuaian') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label ">Rincian *</label>
                            
                            @php
                                $i = 0;
                            @endphp
                            @foreach ($data['data']->detail as $result)
                                @if ($result->debit > 0)
                                    @php
                                        $i++;
                                    @endphp
                                    <div class="mt-2">
                                        @if ($i == 1)
                                            <label class="form-label" id="label-debit">
                                                @switch($data['data']->type)
                                                    @case(0)
                                                        <b>Dana dimasukan ke <i>(Debit)</i> :</b>
                                                        @break
                                                    @case(1)
                                                        <b>Dana dikeluarkan untuk <i>(Debit)</i> :</b>
                                                        @break
                                                    @case(2)
                                                        <b>Debit :</b>
                                                        @break
                                                    @default
                                                        <b>Debit :</b>
                                                @endswitch
                                            </label>
                                        @endif
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Kodeakun</span>
                                            </div>
                                            <select class="form-control sumberdana-input select2" name="top_account[{{$i}}]" id_item="1">
                                                @foreach ($data['account'] as $value)
                                                    <option value="{{$value->code}}" {{$result->account_code==$value->code?'selected':''}} >{{'['.$value->code.'] - '.$value->name}}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" class="type-input" id_item="{{$i}}" name="top_type[{{$i}}]" value="dana_from"/>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control nominal-input-atas money-with-separator" name="top_amount[{{$i}}]" id_item="{{$i}}" value="{{ old('top_amount['.$i.']') ?? number_format($result->debit,2) }}">
                                            <div class="input-group-prepend {{$i!=1?'delete-item-atas':''}}" style="{{$i != 1 ?'cursor: pointer;' : '' }}">
                                                <span class="input-group-text">
                                                    <i class="fa fa-times"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                                

                            <div id="newRowsAtas"></div><!-- newRows -->

                            <div class="text-right mt-2">
                                <a href="#!" class="btn btn-primary" id="topAdd" data-toggle="tooltip" data-state="dark" data-placement="right" title="Tambah kodeakun yang berkaitan dengan transaksi">
                                    <span class="fa fa-plus"></span> Tambah
                                </a>
                            </div>

                            <hr>
                            
                            @php
                                $i = 0;
                            @endphp
                            @foreach ($data['data']->detail as $result)
                                @if ($result->kredit > 0)
                                    @php
                                        $i++;
                                    @endphp
                                    <div class="mt-2">
                                        @if ($i == 1)
                                            <label class="form-label" id="label-kredit">
                                                @switch($data['data']->type)
                                                    @case(0)
                                                        <b>Dana didapatkan dari <i>(Kredit)</i> :</b>
                                                        @break
                                                    @case(1)
                                                        <b>Dana dikeluarkan dari <i>(Kredit)</i> :</b>
                                                        @break
                                                    @case(2)
                                                        <b>Kredit :</b>
                                                        @break
                                                    @default
                                                        <b>Kredit :</b>
                                                @endswitch
                                            </label>
                                        @endif
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Kodeakun</span>
                                            </div>
                                            <select class="form-control tujuandana-input select2" name="bottom_account[{{$i}}]" id_item="{{$i}}" required>
                                                @foreach ($data['account'] as $value)
                                                    <option value="{{$value->code}}" {{$result->account_code==$value->code?'selected':''}}>{{'['.$value->code.'] - '.$value->name}}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" class="type-input" id_item="{{$i}}" name="bottom_type[{{$i}}]" value="dana_to"/>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control m-input nominal-input-bawah money-with-separator" name="bottom_amount[{{$i}}]" min="0" id_item="{{$i}}" value="{{ old('bottom_amount['.$i.']') ?? number_format($result->kredit,2) }}">
                                            <div class="input-group-prepend {{$i!=1?'delete-item-bawah':''}}" style="{{$i != 1 ?'cursor: pointer;' : '' }}">
                                                <span class="input-group-text">
                                                    <i class="fa fa-times"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                                    

                            <div id="newRowsBawah"></div><!-- newRows -->

                            <div class="text-right mt-2">
                                <a href="#!" class="btn btn-primary" id="bottomAdd" data-toggle="tooltip" data-state="dark" data-placement="right" title="Tambah kodeakun yang berkaitan dengan transaksi">
                                    <span class="fa fa-plus"></span> Tambah
                                </a>
                            </div>

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
<script type="text/javascript">	
    function pemasukan() {
        document.getElementById('label-debit').innerHTML = '<b>Dana dimasukan ke <i>(Debit)</i> :</b>';
        document.getElementById('label-kredit').innerHTML = '<b>Dana didapatkan dari <i>(Kredit)</i> :</b>';
    }
    function pengeluaran() {
        document.getElementById('label-debit').innerHTML = '<b>Dana dikeluarkan untuk <i>(Debit)</i> :</b>';
        document.getElementById('label-kredit').innerHTML = '<b>Dana dikeluarkan dari <i>(Kredit)</i> :</b>';
    }
    function penyesuaian() {
        document.getElementById('label-debit').innerHTML = '<b>Debit :</b>';
        document.getElementById('label-kredit').innerHTML = '<b>Kredit :</b>';
    }
    $(document).ready(function(){
        // Add row bagian atas
        var x = <?= $data['rincian_atas'] ?>;
        $(document).on('click', '#topAdd', function () {
            newfield =  '<div class="mt-2">' +
                            '<div class="input-group">' +
            			        '<div class="input-group-prepend"><span class="input-group-text">Kodeakun</span></div>' +
                                '<select class="form-control tujuandana-input select2-jurnal" name="top_account['+x+']" id_item="'+x+'" required> style="width: 50%"' +
                                    '@foreach ($data["account"] as $value)' +
                                        '<option value="{{$value->code}}" {{old("top_account['+x+']")==$value->code?"selected":""}} >' +
                                            '{{"[".$value->code."] - ".$value->name}}' +
                                        '</option>' +
                                    '@endforeach' +
                                '</select>' +
                                '<input type="hidden" class="type-input" id_item="'+x+'" name="top_type['+x+']" value="dana_from"/>' +
                                '<div class="input-group-prepend"><span class="input-group-text">Rp</span></div>' +
                                '<input type="text" class="form-control nominal-input-atas money-with-separator" name="top_amount['+x+']" min="0" id_item="'+x+'" value="{{ old("top_amount['+x+']") ?? 0 }}">' +
                                '<div class="input-group-prepend delete-item-atas" style="cursor: pointer;"><span class="input-group-text"><i class="fa fa-times"></i></span></div>' +
                            '</div>' +
                        '</div>';
            
            $("#newRowsAtas").append(newfield);
            x++;
            $('.select2-jurnal').each(function() {
                $(this).wrap('<div class="position-relative"></div>').select2({
                    placeholder: '--Select--',
                    dropdownParent: $(this).parent()
                });
            });
        });
        $(document).on('click', '.delete-item-atas', function () {
            $(this).parent().parent().remove();
            x--;
        });
        // Add row bagian bawah
        var y = <?= $data["rincian_bawah"] ?>;
        $(document).on('click', '#bottomAdd', function () {
            newfield =  '<div class="mt-2">' +
                            '<div class="input-group">' +
            			        '<div class="input-group-prepend"><span class="input-group-text">Kodeakun</span></div>' +
                                '<select class="form-control tujuandana-input select2-jurnal" name="bottom_account['+y+']" id_item="'+y+'" required> style="width: 50%"' +
                                    '@foreach ($data["account"] as $value)' +
                                        '<option value="{{$value->code}}" {{old("bottom_account['+y+']")==$value->code?"selected":""}} >' +
                                            '{{"[".$value->code."] - ".$value->name}}' +
                                        '</option>' +
                                    '@endforeach' +
                                '</select>' +
                                '<input type="hidden" class="type-input" id_item="'+y+'" name="bottom_type['+y+']" value="dana_from"/>' +
                                '<div class="input-group-prepend"><span class="input-group-text">Rp</span></div>' +
                                '<input type="text" class="form-control nominal-input-bawah money-with-separator" name="bottom_amount['+y+']" min="0" id_item="'+y+'" value="{{ old("bottom_amount['+y+']") ?? 0 }}">' +
                                '<div class="input-group-prepend delete-item-bawah" style="cursor: pointer;"><span class="input-group-text"><i class="fa fa-times"></i></span></div>' +
                            '</div>' +
                        '</div>';
            
            $("#newRowsBawah").append(newfield);
            y++;
            $('.select2-jurnal').each(function() {
                $(this).wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Select value',
                    dropdownParent: $(this).parent()
                });
            });
        });
        $(document).on('click', '.delete-item-bawah', function () {
            $(this).parent().parent().remove();
            y--;
        });
        
        function parseCurrency( num ) {
            
            return parseFloat( num.replace( /,/g, '') );
        }
        $(document).on('submit', '#input-jurnal-form', function () {
            var input_is_ok = true;
            var input_dana_from = 0;
            var input_dana_to = 0;

            $('.no_ref').each(function() {
                ket = $(this).val();
                if (ket == '') {
                    input_is_ok = false;
                    alert('No Ref / No Bukti tidak boleh kosong !');
                    return false;
                }
            });

            $('.name').each(function() {
                ket = $(this).val();
                if (ket == '') {
                    input_is_ok = false;
                    alert('Keterangan tidak boleh kosong !');
                    return false;
                }
            });

            $('.nominal-input-atas').each(function () {
                get_value = $(this).val();
                tmp_val = parseCurrency(get_value);
                if (tmp_val <= 0 || isNaN(tmp_val)) {
                    input_is_ok = false;
                    alert('Nominal tidak boleh kosong !');
                    return false;
                }
                input_dana_from += tmp_val;
            });

            $('.nominal-input-bawah').each(function () {
                get_value = $(this).val();
                tmp_val = parseCurrency(get_value);
                if (tmp_val <= 0 || isNaN(tmp_val)) {
                    input_is_ok = false;
                    alert('Nominal tidak boleh kosong !');
                    return false;
                }
                input_dana_to += tmp_val;
            });

            if (input_is_ok && input_dana_to != input_dana_from) {
                input_is_ok = false;
                alert('Nominal kodeakun sumber dan kodeakun tujuan tidak seimbang, silahkan perbaiki.');
                return false;
            }

            if (input_is_ok) {
                $('.tujuandana-input').each(function () {
                    if ($('.sumberdana-input').val() == $(this).val()) {
                        input_is_ok = false;
                        alert('Kode akun di rincian tidak boleh sama !');
                        return false;
                    }
                });
            }

            if (!input_is_ok)
                return false;
            return true;
        });
    });
</script>
@endsection