@extends('layouts.application')

@section('module', 'Reset Aplikasi')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <div>
                <h4 class="mb-0">UNIT SIMPANAN</h4>
                <div class="mb-0">Semua data simpanan dan jurnal yang berkaitan dengan simpanan akan terhapus.</div>
            </div>
            <button class="btn btn-danger" data-toggle="modal" data-target="#reset-simpanan">Reset</button>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="reset-simpanan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reset Data Simpanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">Anda Yakin?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batalkan</button>
                <a href="{{ route('resetSimpanan') }}" class="btn btn-primary">Lanjutkan</a>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <div>
                <h4 class="mb-0">UNIT TOKO</h4>
                <div class="mb-0">Data yang akan terhapus: Data barang, data penjualan, data pembelian dan jurnal yang berkaitan dengan toko.</div>
            </div>
            <button class="btn btn-danger" data-toggle="modal" data-target="#reset-toko">Reset</button>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="reset-toko" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reset Data Toko</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">Anda Yakin?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batalkan</button>
                <a href="{{ route('resetToko') }}" class="btn btn-primary">Lanjutkan</a>
            </div>
        </div>
    </div>
</div>
@endsection