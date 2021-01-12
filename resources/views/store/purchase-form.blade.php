@extends('layouts.application')

@section('module', 'Data Pembelian Barang')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Data Pembelian Barang</div>
    <form action="{{ route('purchaseConfirm') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-pembelian">
        <div class="card-body">
            @csrf
            <input type="hidden" name="no_faktur" value="{{ str_pad($data['no_faktur'], 9, 0, STR_PAD_LEFT) }}">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">No Faktur *</label>
                        <input type="text" class="form-control" value="{{ old('no_faktur') ?? str_pad($data['no_faktur'], 9, 0, STR_PAD_LEFT) }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Pembelian *</label>
                        <input type="text" class="form-control {{ $errors->has('tanggal_beli')?' is-invalid':'' }} datepicker" placeholder="Tanggal Pembelian" name="tanggal_beli" id="tanggal_beli" value="{{ old('tanggal_beli') ?? date('Y-m-d') }}" required>
                        {!! $errors->first('tanggal_beli', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Tanggal Pembelian.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Suplier</label>
                        <select name="suplier_id" id="" class="form-control select2" required>
                            <option value="">--Pilih--</option>
                            @foreach ($data['suplier'] as $value)
                                <option value="{{ $value->id }}" {{ old('suplier_id') == $value->id ? 'selected' : '' }}>[{{$value->code}}] - {{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control {{ $errors->has('note')?' is-invalid':'' }}" placeholder="Keterangan" name="note" id="note" value="{{ old('note') ?? '' }}">
                        {!! $errors->first('note', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan keterangan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No Ref</label>
                        <input type="text" class="form-control {{ $errors->has('ref_number')?' is-invalid':'' }}" placeholder="No Ref" name="ref_number" id="ref_number" value="{{ old('ref_number') ?? 'TRXT-'.date('YmdHis') }}" required>
                        {!! $errors->first('ref_number', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan No Ref.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alokasi Barang ke</label>
                        <select name="warehouse_id" id="" class="select2 form-control {{ $errors->has('warehouse_id')?' is-invalid':'' }}" required>
                            <option value="0">-- Pusat --</option>
                            @foreach ($data['gudang'] as $item)
                                <option value="{{ $item->id }}">[{{ $item->code }}] - {{ $item->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('warehouse_id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan No Ref.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kode / Nama Barang *</label>
                        <select class="form-control input-tambah-barang"></select>
                        <small class="form-text text-muted">Ketikan kode atau nama barang.</small>
                    </div>

                    <div class="form-group">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Qty Bersih (Kg)</th>
                                    <th>Qty Susut (Kg)</th>
                                    <th>Harga Beli/Kg (Rp)</th>
                                    <th>Harga Total (Rp)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="list-pembelian-barang">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td  colspan="4" style="text-align: right;"><h4>Total</h4></td>
                                    <td align="right">
                                        <h4> <span id="input-total_items">0</span> Items</h4>
                                    </td>
                                    <td>
                                        <h4><span id="input-total_belanja">0</span></h4>
                                    </td>
                                    <td>
                                        <h4></h4>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Diskon</label>
                        <div class="input-group">
                            <input type="text" class="form-control {{ $errors->has('input-diskon')?' is-invalid':'' }} money-without-separator" name="input-diskon" id="input-diskon" value="0">
                            <select name="tipe_diskon" id="tipe_diskon" class="form-control select2">
                                <option value="0">Nominal</option>
                                <option value="1">Persentase</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Besar Diskon</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="hidden" name="diskon" value="0" id="besar_diskon">
                            <input type="text" class="form-control" id="view-besar_diskon" value="0" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Yang Harus Dibayar</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="hidden" name="total" value="0" id="total_belanja">
                            <input type="text" class="form-control" id="view-total_belanja" value="0" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah Bayar *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('total_bayar')?' is-invalid':'' }} money-with-separator" name="total_bayar" id="total_bayar" value="{{ old('total_bayar') ?? 0 }}">
                        </div>
                        {!! $errors->first('total_bayar', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nominal yang dibayarkan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pilih Akun *</label>
                        <select name="account" id="account" class="form-control select2" required>
                            <option value="">--Pilih--</option>
                            @foreach ($data['cash'] as $value)
                                <option value="{{ $value->code }}">[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('account', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun transaksi.</small>
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
    var last_barang_id = 0;
    var load_data_barang_done_stat = false; //true jiga sedang proses loading ajax barang nya
    var load_data_barang_enter_stat = false; //true jika sudah ditekan
    var total_item_barang = 0;
    var harga_jual = {};
    var list_barang = {};

    $(document).ready(function(){
        var_inputbarang = $(".input-tambah-barang").select2({
            placeholder: 'Ketikan kode / nama barang',
            ajax: {
                url: '/getItem',
                formatInputTooShort: function () {
                    return "Ketikan kode / nama barang";
                },
                dataType: 'json',
                delay: 250,
                type: 'GET',
                data: function (params) {

                    load_data_barang_done_stat = false;
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    if (data.length == 1) {

                        if (load_data_barang_enter_stat) {
                            var_inputbarang.select2("close");

                            tambah_barang(data[0]);
                            $(".input-tambah-barang").empty();//hilangkan kembali input serch barang nya
                        }

                    }

                    console.log(data);
                    load_data_barang_done_stat = true;
                    load_data_barang_enter_stat = false;

                    return {
                        results: data
                    };
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1
        });
        $(document).on('keyup', '.select2-search__field', function (e) {
            if (e.which === 13) {
                if (load_data_barang_done_stat == false) {
                    load_data_barang_enter_stat = true;
                }
                return false;
            }
        });
        var_inputbarang.on("select2:select", function (e) {
            tambah_barang(e.params.data);

            $(".input-tambah-barang").empty();//hilangkan kembali input serch barang nya
        });

        var_inputbarang.on("select2:unselect", function (e) {
            var barang_id = e.params.data.id;
            //$('#input_calon_' + jabatan_id).remove();
            alert(barang_id);
        });
        function cek_barang(id) {
            var idbrg = id;
            var retunse;
            $.each($("#list-pembelian-barang tr"), function (index, value) {
                if ($(this).attr("idbarang") == idbrg) {
                    console.log('false');
                    retunse = false;
                } else {
                    console.log('true');
                    retunse = true;

                }
            });
            return retunse;
        }
        function tambah_barang(data) {
            if (harga_jual[data.id]) {
                dQty = $('tr[idbarang="' + data.id + '"] .input-qty');
                dQty.val(addSeparator(parseInt(deleteSeparator(dQty.val()))+1));
            } else {
                total_item_barang += parseInt(1);
                harga_jual[data.id] = data.harga_jual;
                last_barang_id++;
                list_barang[last_barang_id] = data;
                str_row_barang ='<tr idbarang="' + data.id + '" nourutbarang="' + last_barang_id + '" class="list-barang nourutbarang-' + last_barang_id + '">' + 
                                '	<input type="hidden" name="barang[' + last_barang_id + '][item_id]" value="' + data.id + '">' +
                                '	<input type="hidden" name="barang[' + last_barang_id + '][code]" value="' + data.code + '">' +
                                '	<input type="hidden" name="barang[' + last_barang_id + '][name]" value="' + data.name + '">' +
                                '   <td>' + data.code + '</td>' +
                                '   <td>' + data.name + '</td>' +
                                '   <td>' + 
                                '       <input type="text" class="form-control text-right input-qty money-without-separator" name="barang[' + last_barang_id + '][qty]" value="1">' +
                                '   </td>' +
                                '   <td>' + 
                                '       <input type="text" class="form-control text-right input-susut money-without-separator" name="barang[' + last_barang_id + '][susut]" value="0">' +
                                '   </td>' +
                                '   <td>' + 
                                '       <input type="text" class="form-control text-right input-harga_beli_satuan money-with-separator" name="barang[' + last_barang_id + '][harga_beli_satuan]" value="0">' +
                                '   </td>' +
                                '   <td>' +
                                '       <input type="hidden" class="harga_total_satuan" value="0" name="barang[' + last_barang_id + '][harga_total_satuan]">'+
                                '       <span class="harga_total_per_produk_'+last_barang_id +'">0</span>' +
                                '   </td>' +
                                '   <td><a href="#!" class="btn btn-sm icon-btn btn-danger delete-barang-detail"><i class="fa fa-trash-alt"></i></a></td>' +
                                '</tr>';
                $('#list-pembelian-barang').append(str_row_barang);
                $(".tanggal-kadaluarsa").datepicker({
                    autoclose: !0,
                    format: "yyyy-mm-dd",
                    orientation: "auto"
                });
            }
            $('.input-qty').change();
            $('.input-susut').change();
            $('.input-harga_beli_satuan').change();
        }
        $(document).on('click', '.delete-barang-detail', function () {
            nourutbarang = $(this).parent().parent().attr('nourutbarang');
            idbarang = $(this).parent().parent().attr('idbarang');

            total_item_barang--;
            $('#input-total_items').html(total_item_barang);

            //
            //delete data_barang[idbarang];
            delete harga_jual[idbarang];

            $(this).parent().parent().remove();
            calculate_harga_per_produk(this, 'delete');
            calculate_totalbelanja();
            hitung_diskon();
        });

        $(document).on('submit', '#form-pembelian', function () {
            var returnfalse = false;
            

            //jika belum menambahkan barang maka tolak
            if (total_item_barang <= 0) {
                alert('Barang belum diinputkan !');
                return false;
            }

            //pastikan jumlah yang dibayarkan tidak melebihi pembelanjaan
            var total_belanja = $('#total_belanja').val();
            var diskon = $('#besar_diskon').val();
            var total_bayar = deleteSeparator($('#total_bayar').val());

            if (total_belanja <= 0) {
                alert('Total belanja masih kosong.');
                return false;
            }
            
            if (total_bayar > total_belanja) {
                alert('Jumlah bayar melebihi pembelanjaan!');
                return false;
            }

            $('.harga_total_satuan').each(function (i) {
                if(parseFloat($(this).val()) <= 0){
                    alert('Harga barang tidak boleh ada yang kosong!');
                    returnfalse = true;
                    return false;
                }
            });

            if (returnfalse)
                return false;
        });
    });

    $(document).on('change', '.input-qty', function () {
        nama = $(this).attr('name');
        val = $(this).val();

        calculate_harga_per_produk(this);
        calculate_totalbelanja();
        calculate_totalitems();
    });

    $(document).on('change', '.input-susut', function () {
        nama = $(this).attr('name');
        val = $(this).val();

        calculate_harga_per_produk(this);
        calculate_totalbelanja();
        calculate_totalitems();
    });

    $(document).on('change', '.input-harga_beli_satuan', function () {
        nama = $(this).attr('name');
        val = $(this).val();

        calculate_harga_per_produk(this);
        calculate_totalbelanja();
    });

    $(document).on('change', '#input-diskon', function(){
        hitung_diskon();
    });

    $(document).on('change', '#tipe_diskon', function(){
        hitung_diskon();
    });

    $(document).on('keyup', '.money-without-separator', function (e) {
        $(this).mask('000,000,000,000,000,000', {reverse: true});
    });

    function calculate_harga_per_produk(q, tipe = 'add') {
        no_urut = $(q).closest('tr').attr('nourutbarang');

        if (tipe == 'add') {
            harga_beli_satuan = deleteSeparator($('input[name="barang[' + no_urut + '][harga_beli_satuan]"]').val());
            qty = deleteSeparator($('input[name="barang[' + no_urut + '][qty]"]').val());
            susut = deleteSeparator($('input[name="barang[' + no_urut + '][susut]"]').val());
        }else{
            harga_beli_satuan = $('input[name="barang[' + no_urut + '][harga_beli_satuan]"]').val();
            qty = $('input[name="barang[' + no_urut + '][qty]"]').val();
            susut = $('input[name="barang[' + no_urut + '][susut]"]').val();
        }

        harga_total_satuan = harga_beli_satuan*(qty + susut);

        $('input[name="barang[' + no_urut + '][harga_total_satuan]"]').val(harga_total_satuan);

        $('#list-pembelian-barang tr td span.harga_total_per_produk_' + no_urut).html(addSeparator(harga_total_satuan));
    }

    function calculate_totalitems() {
        $('#input-total_items').html(total_item_barang);
    }

    function calculate_totalbelanja() {
        harga_total = 0;

        $('.harga_total_satuan').each(function (i) {
            harga_total += parseFloat($(this).val());
        });

        hitung_diskon();
        $('#input-total_belanja').html(addSeparator(harga_total));
        $('#total_belanja').val(harga_total);
        $('#view-total_belanja').val(addSeparator(harga_total));
    }

    function hitung_diskon(){
        var diskon = deleteSeparator($('#input-diskon').val());
        var tipe = $('#tipe_diskon option:selected').val();
        var besar_total_belanja = $('#total_belanja').val();

        if (tipe == 0) {
            besar_diskon = diskon;
        }else{
            besar_diskon = diskon/100*besar_total_belanja;
        }

        total_belanja = besar_total_belanja - besar_diskon; 

        $('#view-besar_diskon').val(addSeparator(besar_diskon));
        $('#besar_diskon').val(besar_diskon);
        $('#view-total_belanja').val(addSeparator(total_belanja));
    }

    function hitung_bayar(){
        var total_belanja = $('#total_belanja').val(); 
        var diskon = $('#besar_diskon').val(); 
        var bayar = 0;
        
    }

    function addSeparator(x) {
        var parts = x.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

    function deleteSeparator(x){
        return parseFloat(x.replace(/,/g,''));
    }
</script>

@endsection