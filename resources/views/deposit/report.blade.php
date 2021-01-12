@extends('layouts.application')

@section('module', 'Laporan Simpanan')

@section('content')
<div class="row">
    @if (Auth::user()->hasRule('simpananAnggota'))
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title">Daftar Simpanan Anggota</h4>
                    <p class="card-text" style="min-height: 45px;">Laporan total data simpanan anggota berdasarkan wilayah</p>
                    <a href="{{ route('simpananAnggota') }}" class="card-link">Go to Page</a>
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->hasRule('rekapitulasiSimpanan'))
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title">Rekapitulasi Simpanan Anggota</h4>
                    <p class="card-text" style="min-height: 45px;">Laporan keluar masuk saldo simpanan berdasarkan wilayah</p>
                    <a href="{{ route('rekapitulasiSimpanan') }}" class="card-link">Go to Page</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection