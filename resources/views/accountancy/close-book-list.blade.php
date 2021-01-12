@extends('layouts.application')

@section('module', 'Tutup Buku')
@section('content')
<div class="row">
    <div class="col-md-6 col-xl-4" style="">
        <div class="card mb-3">
            <div class="card-body">
                <h4 class="card-title">Bulanan</h4>
                <p class="card-text" style="min-height: 45px;">Menu Tutup Buku Bulanan</p>
                <a href="{{ route('closeMonthlyBookList') }}" class="card-link">Go to Page</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4" style="">
        <div class="card mb-3">
            <div class="card-body">
                <h4 class="card-title">Tahunan</h4>
                <p class="card-text" style="min-height: 45px;">Menu Tutup Buku Tahunan</p>
                <a href="{{ route('closeYearlyBookList') }}" class="card-link">Go to Page</a>
            </div>
        </div>
    </div>
</div>
@endsection