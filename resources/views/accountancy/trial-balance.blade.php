@extends('layouts.application')

@section('module', 'Neraca Saldo')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        @if (isset($data['tbb_id']))
            <input type="hidden" name="tbb_id" value="{{ $data['tbb_id'] }}">
        @endif
        @if (isset($data['tbt_id']))
            <input type="hidden" name="tbt_id" value="{{ $data['tbt_id'] }}">
        @endif
        <div class="col-md-3 mb-3">
            <label class="form-label">Tampilkan</label>
            <select class="select2 form-control" name="view">
                <option value="all" {{$data['view'] == 'all'?'selected':''}}>Semua Akun</option>
                <option value="group" {{$data['view'] == 'group'?'selected':''}}>Kelompok Akun</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Filter Tanggal</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">From</div>
                    <input type="text" class="form-control {{ !isset($data['tbb_id']) && !isset($data['tbt_id']) ? 'datepicker' : '' }}" name="start_date" value="{{$data['start_date']}}" {{ !isset($data['tbb_id']) && !isset($data['tbt_id']) ? '' : 'readonly' }}>
                    <div class="input-group-prepend"><span class="input-group-text">To</div>
                    <input type="text" class="form-control {{ !isset($data['tbb_id']) && !isset($data['tbt_id']) ? 'datepicker' : '' }}" name="end_date" value="{{$data['end_date']}}" {{ !isset($data['tbb_id']) && !isset($data['tbt_id']) ? '' : 'readonly' }}>
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Filter</button>
                </span>
            </div>
        </div>

        <div class="col-md text-right">
            @if (Auth::user()->hasRule('trialBalancePrint'))
                <a href="{{ route('trialBalancePrint', $data['param']) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print Neraca Saldo" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('trialBalanceDownload'))
                <a href="{{ route('trialBalanceDownload', $data['param']) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download neraca saldo">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-1">Neraca Saldo</h4>
                <h6 class="mb-1">Per {{ date('d-m-Y', strtotime($data['end_date'])) }}</h6>
            </div>
            <div class="table-responsive">
                @if ($data['view'] == 'all')
                    <table class="table card-table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center align-middle" rowspan="2">#</th>
                                <th rowspan="2" class="align-middle">Kode Akun</th>
                                <th rowspan="2" class="align-middle">Nama Akun</th>
                                <th colspan="2" class="text-center">Saldo Awal (Rp)</th>
                                <th colspan="2" class="text-center">Mutasi (Rp)</th>
                                <th colspan="2" class="text-center">Saldo Akhir (Rp)</th>
                            </tr>
                            <tr>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Kredit</th>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Kredit</th>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = $total_debit = $total_kredit = $total_saldo_awal_debit = $total_saldo_awal_kredit = $total_saldo_penyesuaian_debit = $total_saldo_penyesuaian_kredit = 0;
                            @endphp
                            @foreach ($data['data'] as $value)
                                @php
                                    $i++;
                                    if ($value->type == 0) {
                                        if ($value->saldo_awal >= 0) {
                                            $saldo_awal_debit = $value->saldo_awal;
                                            $saldo_awal_kredit = 0;
                                        }else{
                                            $saldo_awal_kredit = $value->saldo_awal * -1;
                                            $saldo_awal_debit = 0;
                                        }

                                        if ($value->saldo_penyesuaian >= 0) {
                                            $saldo_penyesuaian_debit = $value->saldo_penyesuaian;
                                            $saldo_penyesuaian_kredit = 0;
                                        }else{
                                            $saldo_penyesuaian_kredit = $value->saldo_penyesuaian * -1;
                                            $saldo_penyesuaian_debit = 0;
                                        }
                                    }else{
                                        if ($value->saldo_awal >= 0) {
                                            $saldo_awal_kredit = $value->saldo_awal;
                                            $saldo_awal_debit = 0;
                                        }else{
                                            $saldo_awal_kredit = 0;
                                            $saldo_awal_debit = $value->saldo_awal * -1;
                                        }

                                        if($value->saldo_penyesuaian >= 0){
                                            $saldo_penyesuaian_kredit = $value->saldo_penyesuaian;
                                            $saldo_penyesuaian_debit = 0;
                                        }else{
                                            $saldo_penyesuaian_kredit = 0;
                                            $saldo_penyesuaian_debit = $value->saldo_penyesuaian * -1;
                                        }
                                    }
                                    $total_saldo_awal_debit += $saldo_awal_debit;
                                    $total_saldo_awal_kredit += $saldo_awal_kredit;
                                    $total_debit += $value->debit;
                                    $total_kredit += $value->kredit;
                                    $total_saldo_penyesuaian_debit += $saldo_penyesuaian_debit;
                                    $total_saldo_penyesuaian_kredit += $saldo_penyesuaian_kredit;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $i }}</td>
                                    <td>{{ $value->code }}</td>
                                    <td>{{ $value->name }}</td>
                                    <td class="text-right">{{ number_format($saldo_awal_debit, 2, ',','.') }}</td>
                                    <td class="text-right">{{ number_format($saldo_awal_kredit, 2, ',','.') }}</td>
                                    <td class="text-right">{{ number_format($value->debit, 2, ',','.') }}</td>
                                    <td class="text-right">{{ number_format($value->kredit, 2, ',','.') }}</td>
                                    <td class="text-right">{{ number_format($saldo_penyesuaian_debit, 2, ',','.') }}</td>
                                    <td class="text-right">{{ number_format($saldo_penyesuaian_kredit, 2, ',','.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="text-right">
                                <td colspan="3"><b>Jumlah :</b></td>
                                <td><b>{{ number_format($total_saldo_awal_debit, 2, ',', '.') }}</b></td>
                                <td><b>{{ number_format($total_saldo_awal_kredit, 2, ',', '.') }}</b></td>
                                <td><b>{{ number_format($total_debit, 2, ',', '.') }}</b></td>
                                <td><b>{{ number_format($total_kredit, 2, ',', '.') }}</b></td>
                                <td><b>{{ number_format($total_saldo_penyesuaian_debit, 2, ',', '.') }}</b></td>
                                <td><b>{{ number_format($total_saldo_penyesuaian_kredit, 2, ',', '.') }}</b></td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <table class="table card-table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center align-middle" rowspan="2">#</th>
                                <th rowspan="2" class="align-middle">Kelompok Akun</th>
                                <th colspan="2" class="text-center">Saldo Awal (Rp)</th>
                                <th colspan="2" class="text-center">Mutasi (Rp)</th>
                                <th colspan="2" class="text-center">Saldo Akhir (Rp)</th>
                            </tr>
                            <tr>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Kredit</th>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Kredit</th>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = $total_debit = $total_kredit = $total_saldo_awal_debit = $total_saldo_awal_kredit = $total_saldo_penyesuaian_debit = $total_saldo_penyesuaian_kredit = 0;
                            @endphp
                            @foreach ($data['group'] as $value)
                                @php
                                    $i++;
                                    if ($value->type == 0) {
                                        if ($value->saldo_awal >= 0) {
                                            $saldo_awal_debit = $value->saldo_awal;
                                            $saldo_awal_kredit = 0;
                                        }else{
                                            $saldo_awal_kredit = $value->saldo_awal * -1;
                                            $saldo_awal_debit = 0;
                                        }

                                        if ($value->saldo_penyesuaian >= 0) {
                                            $saldo_penyesuaian_debit = $value->saldo_penyesuaian;
                                            $saldo_penyesuaian_kredit = 0;
                                        }else{
                                            $saldo_penyesuaian_kredit = $value->saldo_penyesuaian * -1;
                                            $saldo_penyesuaian_debit = 0;
                                        }
                                    }else{
                                        if ($value->saldo_awal >= 0) {
                                            $saldo_awal_kredit = $value->saldo_awal;
                                            $saldo_awal_debit = 0;
                                        }else{
                                            $saldo_awal_kredit = 0;
                                            $saldo_awal_debit = $value->saldo_awal * -1;
                                        }

                                        if($value->saldo_penyesuaian >= 0){
                                            $saldo_penyesuaian_kredit = $value->saldo_penyesuaian;
                                            $saldo_penyesuaian_debit = 0;
                                        }else{
                                            $saldo_penyesuaian_kredit = 0;
                                            $saldo_penyesuaian_debit = $value->saldo_penyesuaian * -1;
                                        }
                                    }
                                    $total_saldo_awal_debit += $saldo_awal_debit;
                                    $total_saldo_awal_kredit += $saldo_awal_kredit;
                                    $total_debit += $value->debit;
                                    $total_kredit += $value->kredit;
                                    $total_saldo_penyesuaian_debit += $saldo_penyesuaian_debit;
                                    $total_saldo_penyesuaian_kredit += $saldo_penyesuaian_kredit;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $i }}</td>
                                    <td>{{ $value->name }}</td>
                                    <td class="text-right">{{ number_format($saldo_awal_debit, 2, ',','.') }}</td>
                                    <td class="text-right">{{ number_format($saldo_awal_kredit, 2, ',','.') }}</td>
                                    <td class="text-right">{{ number_format($value->debit, 2, ',','.') }}</td>
                                    <td class="text-right">{{ number_format($value->kredit, 2, ',','.') }}</td>
                                    <td class="text-right">{{ number_format($saldo_penyesuaian_debit, 2, ',','.') }}</td>
                                    <td class="text-right">{{ number_format($saldo_penyesuaian_kredit, 2, ',','.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="text-right">
                                <td colspan="2"><b>Jumlah :</b></td>
                                <td><b>{{ number_format($total_saldo_awal_debit, 2, ',', '.') }}</b></td>
                                <td><b>{{ number_format($total_saldo_awal_kredit, 2, ',', '.') }}</b></td>
                                <td><b>{{ number_format($total_debit, 2, ',', '.') }}</b></td>
                                <td><b>{{ number_format($total_kredit, 2, ',', '.') }}</b></td>
                                <td><b>{{ number_format($total_saldo_penyesuaian_debit, 2, ',', '.') }}</b></td>
                                <td><b>{{ number_format($total_saldo_penyesuaian_kredit, 2, ',', '.') }}</b></td>
                            </tr>
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection