@extends('layouts.application')

@section('module', 'Laporan Toko')

@section('content')
<div class="row">
    @if (Auth::user()->hasRule('storeReportSaleCash'))
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title">Penjualan Tunai</h4>
                    <p class="card-text" style="min-height: 45px;">Laporan penjualan tunai anggota dan non anggota</p>
                    <a href="{{ route('storeReportSaleCash') }}" class="card-link">Go to Page</a>
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->hasRule('storeReportSaleDebt'))
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title">Penjualan Kredit</h4>
                    <p class="card-text" style="min-height: 45px;">Laporan penjualan kredit anggota dan non anggota</p>
                    <a href="{{ route('storeReportSaleDebt') }}" class="card-link">Go to Page</a>
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->hasRule('storeReportItemStock'))
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title">Persediaan Barang</h4>
                    <p class="card-text" style="min-height: 45px;">Laporan persediaan barang gudang dan pusat.</p>
                    <a href="{{ route('storeReportItemStock') }}" class="card-link">Go to Page</a>
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->hasRule('storeReportRegion'))
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title">Penjualan Wilayah</h4>
                    <p class="card-text" style="min-height: 45px;">Laporan penjualan bedasarkan wilayah</p>
                    <a href="{{ route('storeReportRegion') }}" class="card-link">Go to Page</a>
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->hasRule('storeReportMember'))
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title">Penjualan Anggota & Non Anggtoa</h4>
                    <p class="card-text" style="min-height: 45px;">Laporan penjualan bedasarkan anggota dan Non Anggota</p>
                    <a href="{{ route('storeReportMember') }}" class="card-link">Go to Page</a>
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->hasRule('storeReportUtang'))
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title">Rekapitulasi Utang</h4>
                    <p class="card-text" style="min-height: 45px;">Laporan rekapitulasi utang dan buku besar pembantu pembelian kredit</p>
                    <a href="{{ route('storeReportUtang') }}" class="card-link">Go to Page</a>
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->hasRule('storeReportPiutang'))
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title">Rekapitulasi Piutang</h4>
                    <p class="card-text" style="min-height: 45px;">Laporan rekapitulasi piutang dan buku besar pembantu penjualan kredit</p>
                    <a href="{{ route('storeReportPiutang') }}" class="card-link">Go to Page</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection