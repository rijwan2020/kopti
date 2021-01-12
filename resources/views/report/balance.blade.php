@extends('layouts.application')

@section('module', 'Neraca')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        @if (isset($data['tbb_id']))
            <input type="hidden" name="tbb_id" value="{{ $data['tbb_id'] }}">
        @endif
        @if (isset($data['tbt_id']))
            <input type="hidden" name="tbt_id" value="{{ $data['tbt_id'] }}">
        @endif
        <div class="col-md-2 mb-3">
            <label for="" class="form-label">Tampilkan</label>
            <select name="view" id="view" class="select2 form-control">
                <option value="all" {{ $data['view'] == 'all' ? 'selected' : '' }}>Semua Akun</option>
                <option value="group" {{ $data['view'] == 'group' ? 'selected' : '' }}>Kelompok Akun</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Filter Tanggal</label>
            <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                    <input type="text" class="form-control {{ !isset($data['tbb_id']) && !isset($data['tbt_id']) ? 'datepicker' : '' }}" name="end_date" value="{{$data['end_date']}}" {{ !isset($data['tbb_id']) && !isset($data['tbt_id']) ? '' : 'readonly' }}>
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Filter</button>
                </span>
            </div>
        </div>

        <div class="col-md text-right">
            @if ($data['view'] == 'group' && Auth::user()->hasRule('balanceDescription'))
                <a href="{{ route('balanceDescription', $data['param']) }}" class="btn my-1 btn-info" data-toggle="tooltip" data-state="dark" title="Penjelasan Neraca">
                    <i class="fa fa-bars"></i>
                    Penjelasan
                </a>
            @endif
            @if (Auth::user()->hasRule('balancePrint'))
                <a href="{{ route('balancePrint', $data['param']) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print Neraca" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('balanceDownload'))
                <a href="{{ route('balanceDownload', $data['param']) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download Neraca">
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
                <h4 class="mb-1">Neraca</h4>
                <h6 class="mb-1">Periode {{ date('d M Y', strtotime($data['end_date'])) }}</h6>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="table-responsive">
                        @if ($data['view'] == 'all')
                            <table class="table card-table table-bordered">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th colspan="5" class="h3">Aktiva</th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Kode Akun</th>
                                        <th>Nama Akun</th>
                                        <th>{{ date('d M Y', strtotime($data['end_date'])) }} (Rp)</th>
                                        <th>31 Dec {{ date('Y', strtotime('-1 year', strtotime($data['start_date']) ) ) }} (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>I</th>
                                        <th colspan="4">Aktiva Lancar</th>
                                    </tr>
                                    @php
                                        $i = $aktiva_lancar = $aktiva_lancar_lalu = 0;
                                    @endphp
                                    @foreach ($data['data'] as $key => $value)
                                        @if ($value->code[1] == 1 && $value->code[4] == 1 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 0) {
                                                    $saldo = $value->saldo_penyesuaian;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo_penyesuaian * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $aktiva_lancar += $saldo;
                                                $aktiva_lancar_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>1.{{ $i }}</td>
                                                <td>{{ $value->code }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="3">Total Aktiva Lancar :</th>
                                        <th>{{ $aktiva_lancar >= 0 ? number_format($aktiva_lancar,2,',','.') : '('.number_format($aktiva_lancar*-1,2,',','.').')' }}</th>
                                        <th>{{ $aktiva_lancar_lalu >= 0 ? number_format($aktiva_lancar_lalu,2,',','.') : '('.number_format($aktiva_lancar_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    <tr>
                                        <th>II</th>
                                        <th colspan="4">Investasi</th>
                                    </tr>
                                    @php
                                        $i = $investasi = $investasi_lalu = 0;
                                    @endphp
                                    @foreach ($data['data'] as $key => $value)
                                        @if ($value->code[1] == 1 && $value->code[4] == 2 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 0) {
                                                    $saldo = $value->saldo_penyesuaian;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo_penyesuaian * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $investasi += $saldo;
                                                $investasi_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>2.{{ $i }}</td>
                                                <td>{{ $value->code }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="3">Total Investasi :</th>
                                        <th>{{ $investasi >= 0 ? number_format($investasi,2,',','.') : '('.number_format($investasi*-1,2,',','.').')' }}</th>
                                        <th>{{ $investasi_lalu >= 0 ? number_format($investasi_lalu,2,',','.') : '('.number_format($investasi_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    <tr>
                                        <th>III</th>
                                        <th colspan="4">Aktiva Tetap</th>
                                    </tr>
                                    @php
                                        $i = $aktiva_tetap = $aktiva_tetap_lalu = 0;
                                    @endphp
                                    @foreach ($data['data'] as $key => $value)
                                        @if ($value->code[1] == 1 && $value->code[4] == 3 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu !=0))
                                            @php
                                                $i++;
                                                if ($value->type == 0) {
                                                    $saldo = $value->saldo_penyesuaian;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo_penyesuaian * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $aktiva_tetap += $saldo;
                                                $aktiva_tetap_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>3.{{ $i }}</td>
                                                <td>{{ $value->code }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="3">Total Aktiva Tetap :</th>
                                        <th>{{ $aktiva_tetap >= 0 ? number_format($aktiva_tetap,2,',','.') : '('.number_format($aktiva_tetap*-1,2,',','.').')' }}</th>
                                        <th>{{ $aktiva_tetap_lalu >= 0 ? number_format($aktiva_tetap_lalu,2,',','.') : '('.number_format($aktiva_tetap_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    @php
                                        $total_aktiva = $aktiva_lancar + $investasi + $aktiva_tetap;
                                        $total_aktiva_lalu = $aktiva_lancar_lalu + $investasi_lalu + $aktiva_tetap_lalu;
                                    @endphp
                                    <tr class="text-right">
                                        <th colspan="3">Total Aktiva :</th>
                                        <th>{{ $total_aktiva >= 0 ? number_format($total_aktiva,2,',','.') : '('.number_format($total_aktiva*-1,2,',','.').')' }}</th>
                                        <th>{{ $total_aktiva_lalu >= 0 ? number_format($total_aktiva_lalu,2,',','.') : '('.number_format($total_aktiva_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <table class="table card-table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th colspan="4" class="text-center h3">Aktiva</th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Kelompok Akun</th>
                                        <th class="text-center">{{ date('d M Y', strtotime($data['end_date'])) }} (Rp)</th>
                                        <th class="text-center">31 Dec {{ date('Y', strtotime('-1 year', strtotime($data['start_date']) ) ) }} (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>I</th>
                                        <th colspan="3">Aktiva Lancar</th>
                                    </tr>
                                    @php
                                        $i = $aktiva_lancar = $aktiva_lancar_lalu = 0;
                                    @endphp
                                    @foreach ($data['group'] as $key => $value)
                                        @if ($value->account_id == 6 && ($value->saldo !=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 0) {
                                                    $saldo = $value->saldo;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $aktiva_lancar += $saldo;
                                                $aktiva_lancar_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>1.{{ $i }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="2">Total Aktiva Lancar :</th>
                                        <th>{{ $aktiva_lancar >= 0 ? number_format($aktiva_lancar,2,',','.') : '('.number_format($aktiva_lancar*-1,2,',','.').')' }}</th>
                                        <th>{{ $aktiva_lancar_lalu >= 0 ? number_format($aktiva_lancar_lalu,2,',','.') : '('.number_format($aktiva_lancar_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    <tr>
                                        <th>II</th>
                                        <th colspan="3">Investasi</th>
                                    </tr>
                                    @php
                                        $i = $investasi = $investasi_lalu = 0;
                                    @endphp
                                    @foreach ($data['group'] as $key => $value)
                                        @if ($value->account_id == 7 && ($value->saldo !=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 0) {
                                                    $saldo = $value->saldo;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $investasi += $saldo;
                                                $investasi_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>2.{{ $i }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="2">Total Investasi :</th>
                                        <th>{{ $investasi >= 0 ? number_format($investasi,2,',','.') : '('.number_format($investasi*-1,2,',','.').')' }}</th>
                                        <th>{{ $investasi_lalu >= 0 ? number_format($investasi_lalu,2,',','.') : '('.number_format($investasi_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    <tr>
                                        <th>III</th>
                                        <th colspan="3">Aktiva Tetap</th>
                                    </tr>
                                    @php
                                        $i = $aktiva_tetap = $aktiva_tetap_lalu = 0;
                                    @endphp
                                    @foreach ($data['group'] as $key => $value)
                                        @if ($value->account_id == 8 && ($value->saldo !=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 0) {
                                                    $saldo = $value->saldo;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $aktiva_tetap += $saldo;
                                                $aktiva_tetap_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>3.{{ $i }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="2">Total Aktiva Tetap :</th>
                                        <th>{{ $aktiva_tetap >= 0 ? number_format($aktiva_tetap,2,',','.') : '('.number_format($aktiva_tetap*-1,2,',','.').')' }}</th>
                                        <th>{{ $aktiva_tetap_lalu >= 0 ? number_format($aktiva_tetap_lalu,2,',','.') : '('.number_format($aktiva_tetap_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    @php
                                        $total_aktiva = $aktiva_lancar + $investasi + $aktiva_tetap;
                                        $total_aktiva_lalu = $aktiva_lancar_lalu + $investasi_lalu + $aktiva_tetap_lalu;
                                    @endphp
                                    <tr class="text-right">
                                        <th colspan="2">Total Aktiva :</th>
                                        <th>{{ $total_aktiva >= 0 ? number_format($total_aktiva,2,',','.') : '('.number_format($total_aktiva*-1,2,',','.').')' }}</th>
                                        <th>{{ $total_aktiva_lalu >= 0 ? number_format($total_aktiva_lalu,2,',','.') : '('.number_format($total_aktiva_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                        
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive">
                        @if ($data['view'] == 'all')
                            <table class="table card-table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th colspan="5" class="text-center h3">Pasiva</th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Kode Akun</th>
                                        <th>Nama Akun</th>
                                        <th class="text-center">{{ date('d M Y', strtotime($data['end_date'])) }} (Rp)</th>
                                        <th class="text-center">31 Dec {{ date('Y', strtotime('-1 year', strtotime($data['start_date']) ) ) }} (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>IV</th>
                                        <th colspan="4">Kewajiban Jangka Pendek</th>
                                    </tr>
                                    @php
                                        $i = $kewajiban_jk_pendek = $kewajiban_jk_pendek_lalu = 0;
                                    @endphp
                                    @foreach ($data['data'] as $key => $value)
                                        @if ($value->code[1] == 2 && $value->code[4] == 1 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 1) {
                                                    $saldo = $value->saldo_penyesuaian;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo_penyesuaian * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $kewajiban_jk_pendek += $saldo;
                                                $kewajiban_jk_pendek_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>4.{{ $i }}</td>
                                                <td>{{ $value->code }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="3">Total Kewajiban Jangka Pendek :</th>
                                        <th>{{ $kewajiban_jk_pendek >= 0 ? number_format($kewajiban_jk_pendek,2,',','.') : '('.number_format($kewajiban_jk_pendek*-1,2,',','.').')' }}</th>
                                        <th>{{ $kewajiban_jk_pendek_lalu >= 0 ? number_format($kewajiban_jk_pendek_lalu,2,',','.') : '('.number_format($kewajiban_jk_pendek_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    <tr>
                                        <th>V</th>
                                        <th colspan="4">Kewajiban Jangka Panjang</th>
                                    </tr>
                                    @php
                                        $i = $kewajiban_jk_panjang = $kewajiban_jk_panjang_lalu = 0;
                                    @endphp
                                    @foreach ($data['data'] as $key => $value)
                                        @if ($value->code[1] == 2 && $value->code[4] == 2 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 1) {
                                                    $saldo = $value->saldo_penyesuaian;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo_penyesuaian * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $kewajiban_jk_panjang += $saldo;
                                                $kewajiban_jk_panjang_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>5.{{ $i }}</td>
                                                <td>{{ $value->code }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="3">Total Kewajiban Jangka Panjang :</th>
                                        <th>{{ $kewajiban_jk_panjang >= 0 ? number_format($kewajiban_jk_panjang,2,',','.') : '('.number_format($kewajiban_jk_panjang*-1,2,',','.').')' }}</th>
                                        <th>{{ $kewajiban_jk_panjang_lalu >= 0 ? number_format($kewajiban_jk_panjang_lalu,2,',','.') : '('.number_format($kewajiban_jk_panjang_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    <tr>
                                        <th>VI</th>
                                        <th colspan="4">Modal</th>
                                    </tr>
                                    @php
                                        $i = $modal = $modal_lalu = 0;
                                    @endphp
                                    @foreach ($data['data'] as $key => $value)
                                        @if ($value->code[1] == 3 && $value->code[4] != 4 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 1) {
                                                    $saldo = $value->saldo_penyesuaian;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo_penyesuaian * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $modal += $saldo;
                                                $modal_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>6.{{ $i }}</td>
                                                <td>{{ $value->code }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="3">Total Modal :</th>
                                        <th>{{ $modal >= 0 ? number_format($modal,2,',','.') : '('.number_format($modal*-1,2,',','.').')' }}</th>
                                        <th>{{ $modal_lalu >= 0 ? number_format($modal_lalu,2,',','.') : '('.number_format($modal_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    <tr>
                                        <th>VII</th>
                                        <th colspan="4">PHU</th>
                                    </tr>
                                    @php
                                        $i = $phu = $phu_lalu = 0;
                                    @endphp
                                    @foreach ($data['data'] as $key => $value)
                                        @if ($value->code[1] == 3 && $value->code[4] == 4 && ($value->saldo_penyesuaian!=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 1) {
                                                    $saldo = $value->saldo_penyesuaian;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo_penyesuaian * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $phu += $saldo;
                                                $phu_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>7.{{ $i }}</td>
                                                <td>{{ $value->code }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="3">Total PHU :</th>
                                        <th>{{ $phu >= 0 ? number_format($phu,2,',','.') : '('.number_format($phu*-1,2,',','.').')' }}</th>
                                        <th>{{ $phu_lalu >= 0 ? number_format($phu_lalu,2,',','.') : '('.number_format($phu_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    @php
                                        $total_pasiva = $kewajiban_jk_pendek + $kewajiban_jk_panjang + $modal + $phu;
                                        $total_pasiva_lalu = $kewajiban_jk_pendek_lalu + $kewajiban_jk_panjang_lalu + $modal_lalu + $phu_lalu;
                                    @endphp
                                    <tr class="text-right">
                                        <th colspan="3">Total Pasiva :</th>
                                        <th>{{ $total_pasiva >= 0 ? number_format($total_pasiva,2,',','.') : '('.number_format($total_pasiva*-1,2,',','.').')' }}</th>
                                        <th>{{ $total_pasiva_lalu >= 0 ? number_format($total_pasiva_lalu,2,',','.') : '('.number_format($total_pasiva_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <table class="table card-table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th colspan="4" class="text-center h3">Pasiva</th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Kelompok Akun</th>
                                        <th class="text-center">{{ date('d M Y', strtotime($data['end_date'])) }} (Rp)</th>
                                        <th class="text-center">31 Dec {{ date('Y', strtotime('-1 year', strtotime($data['start_date']) ) ) }} (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>IV</th>
                                        <th colspan="3">Kewajiban Jangka Pendek</th>
                                    </tr>
                                    @php
                                        $i = $kewajiban_jk_pendek = $kewajiban_jk_pendek_lalu = 0;
                                    @endphp
                                    @foreach ($data['group'] as $key => $value)
                                        @if ($value->account_id == 9 && ($value->saldo !=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 1) {
                                                    $saldo = $value->saldo;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $kewajiban_jk_pendek += $saldo;
                                                $kewajiban_jk_pendek_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>4.{{ $i }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="2">Total Kewajiban Jangka Pendek :</th>
                                        <th>{{ $kewajiban_jk_pendek >= 0 ? number_format($kewajiban_jk_pendek,2,',','.') : '('.number_format($kewajiban_jk_pendek*-1,2,',','.').')' }}</th>
                                        <th>{{ $kewajiban_jk_pendek_lalu >= 0 ? number_format($kewajiban_jk_pendek_lalu,2,',','.') : '('.number_format($kewajiban_jk_pendek_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    <tr>
                                        <th>V</th>
                                        <th colspan="3">Kewajiban Jangka Panjang</th>
                                    </tr>
                                    @php
                                        $i = $kewajiban_jk_panjang = $kewajiban_jk_panjang_lalu = 0;
                                    @endphp
                                    @foreach ($data['group'] as $key => $value)
                                        @if ($value->account_id == 10 && ($value->saldo !=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 1) {
                                                    $saldo = $value->saldo;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $kewajiban_jk_panjang += $saldo;
                                                $kewajiban_jk_panjang_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>5.{{ $i }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="2">Total Kewajiban Jangka Panjang :</th>
                                        <th>{{ $kewajiban_jk_panjang >= 0 ? number_format($kewajiban_jk_panjang,2,',','.') : '('.number_format($kewajiban_jk_panjang*-1,2,',','.').')' }}</th>
                                        <th>{{ $kewajiban_jk_panjang_lalu >= 0 ? number_format($kewajiban_jk_panjang_lalu,2,',','.') : '('.number_format($kewajiban_jk_panjang_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    <tr>
                                        <th>VI</th>
                                        <th colspan="3">Modal</th>
                                    </tr>
                                    @php
                                        $i = $modal = $modal_lalu = 0;
                                    @endphp
                                    @foreach ($data['group'] as $key => $value)
                                        @if (in_array($value->account_id, [11,12,13]) && ($value->saldo !=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 1) {
                                                    $saldo = $value->saldo;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $modal += $saldo;
                                                $modal_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>6.{{ $i }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="2">Total Modal :</th>
                                        <th>{{ $modal >= 0 ? number_format($modal,2,',','.') : '('.number_format($modal*-1,2,',','.').')' }}</th>
                                        <th>{{ $modal_lalu >= 0 ? number_format($modal_lalu,2,',','.') : '('.number_format($modal_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    <tr>
                                        <th>VII</th>
                                        <th colspan="3">PHU</th>
                                    </tr>
                                    @php
                                        $i = $phu = $phu_lalu = 0;
                                    @endphp
                                    @foreach ($data['group'] as $key => $value)
                                        @if ($value->account_id == 14 && ($value->saldo !=0 || $value->saldo_tahun_lalu != 0))
                                            @php
                                                $i++;
                                                if ($value->type == 1) {
                                                    $saldo = $value->saldo;
                                                    $saldo_lalu = $value->saldo_tahun_lalu;
                                                }else{
                                                    $saldo = $value->saldo * -1;
                                                    $saldo_lalu = $value->saldo_tahun_lalu * -1;
                                                }
                                                $phu += $saldo;
                                                $phu_lalu += $saldo_lalu;
                                            @endphp
                                            <tr>
                                                <td>7.{{ $i }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                                                <td class="text-right">{{ $saldo_lalu >= 0 ? number_format($saldo_lalu,2,',','.') : '('.number_format($saldo_lalu*-1,2,',','.').')' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="text-right">
                                        <th colspan="2">Total PHU :</th>
                                        <th>{{ $phu >= 0 ? number_format($phu,2,',','.') : '('.number_format($phu*-1,2,',','.').')' }}</th>
                                        <th>{{ $phu_lalu >= 0 ? number_format($phu_lalu,2,',','.') : '('.number_format($phu_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                    @php
                                        $total_pasiva = $kewajiban_jk_pendek + $kewajiban_jk_panjang + $modal + $phu;
                                        $total_pasiva_lalu = $kewajiban_jk_pendek_lalu + $kewajiban_jk_panjang_lalu + $modal_lalu + $phu_lalu;
                                    @endphp
                                    <tr class="text-right">
                                        <th colspan="2">Total Pasiva :</th>
                                        <th>{{ $total_pasiva >= 0 ? number_format($total_pasiva,2,',','.') : '('.number_format($total_pasiva*-1,2,',','.').')' }}</th>
                                        <th>{{ $total_pasiva_lalu >= 0 ? number_format($total_pasiva_lalu,2,',','.') : '('.number_format($total_pasiva_lalu*-1,2,',','.').')' }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection