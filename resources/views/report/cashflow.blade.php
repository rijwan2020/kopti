@extends('layouts.application')

@section('module', 'Neraca')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        <div class="col-md-2 mb-3">
            <label for="" class="form-label">Tampilan</label>
            <select name="view" id="view" class="select2 form-control">
                <option value="all" {{ $data['view'] == 'all' ? 'selected' : '' }}>Semua Akun</option>
                <option value="group" {{ $data['view'] == 'group' ? 'selected' : '' }}>Kelompok Akun</option>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="" class="form-label">Pilih Akun</label>
            <select name="code" id="code" class="select2 form-control">
                @foreach ($data['cash'] as $value)
                    <option value="{{ $value->code }}" {{ $data['code'] == $value['code'] ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Filter Tanggal</label>
            <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                    <input type="text" class="form-control datepicker" name="end_date" value="{{$data['end_date']}}">
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Filter</button>
                </span>
            </div>
        </div>

        <div class="col-md text-right">
            @if (Auth::user()->hasRule('cashflowPrint'))
                <a href="{{ route('cashflowPrint', ['end_date' => $data['end_date'], 'code' => $data['code'], 'view' => $data['view']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print Arus {{ $data['account']->name }}" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('cashflowDownload'))
                <a href="{{ route('cashflowDownload', ['end_date' => $data['end_date'], 'code' => $data['code'], 'view' => $data['view']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download Arus {{ $data['account']->name }}">
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
                <h4 class="mb-1">Arus {{ $data['account']->name }}</h4>
                <h6 class="mb-1">Periode {{ date('d M Y', strtotime($data['end_date'])) }}</h6>
            </div>
            <div class="table-responsive">
                @if ($data['view'] == 'all')
                    <table class="table card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Kode Akun</th>
                                <th>Nama Akun</th>
                                <th class="text-center">Penambahan (Rp)</th>
                                <th class="text-center">Pengurangan (Rp)</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Aktivitas Operasional --}}
                            <tr>
                                <td class="h5" colspan="6">Aktivitas Operasional</td>
                            </tr>
                            @php
                                $total_penambahan_opr = $total_pengurangan_opr = $i = 0;
                            @endphp
                            @foreach ($data['data'] as $value)
                                @if ($value->account_code[1] != 3 || ($value->account_code[1] == 1 && $value->account_code[4] != 2))
                                    @php
                                        $i++;
                                        if ($data['account']->type == 0) {
                                            $penambahan = $value->kredit;
                                            $pengurangan = $value->debit;
                                        }else {
                                            $penambahan = $value->debit;
                                            $pengurangan = $value->kredit;
                                        }
                                        $total_penambahan_opr += $penambahan;
                                        $total_pengurangan_opr += $pengurangan;
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $value->account_code }}</td>
                                        <td>{{ $value->account->name }}</td>
                                        <td class="text-right">{{ number_format($penambahan, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr class="text-right">
                                <th colspan="3">Jumlah :</th>
                                <th>{{ number_format($total_penambahan_opr, 2, ',', '.') }}</th>
                                <th>{{ number_format($total_pengurangan_opr, 2, ',', '.') }}</th>
                                <th></th>
                            </tr>
                            @php
                                $total_opr = $total_penambahan_opr - $total_pengurangan_opr;
                            @endphp
                            <tr class="text-right">
                                <th colspan="5">Total Aktivitas Operasional :</th>
                                <th class="text-right">{{ $total_opr < 0 ? '('.number_format($total_opr*-1, 2, ',', '.').')' : number_format($total_opr, 2, ',', '.') }}</th>
                            </tr>
                            
                            {{-- Aktivitas Investasi --}}
                            <tr>
                                <td class="h5" colspan="6">Aktivitas Investasi</td>
                            </tr>
                            @php
                                $total_penambahan_inv = $total_pengurangan_inv = $i = 0;
                            @endphp
                            @foreach ($data['data'] as $value)
                                @if ($value->account_code[1] == 1 && $value->account_code[4] == 2)
                                    @php
                                        $i++;
                                        if ($data['account']->type == 0) {
                                            $penambahan = $value->kredit;
                                            $pengurangan = $value->debit;
                                        }else {
                                            $penambahan = $value->debit;
                                            $pengurangan = $value->kredit;
                                        }
                                        $total_penambahan_inv += $penambahan;
                                        $total_pengurangan_inv += $pengurangan;
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $value->account_code }}</td>
                                        <td>{{ $value->account->name }}</td>
                                        <td class="text-right">{{ number_format($penambahan, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr class="text-right">
                                <th colspan="3">Jumlah :</th>
                                <th>{{ number_format($total_penambahan_inv, 2, ',', '.') }}</th>
                                <th>{{ number_format($total_pengurangan_inv, 2, ',', '.') }}</th>
                                <th></th>
                            </tr>
                            @php
                                $total_inv = $total_penambahan_inv - $total_pengurangan_inv;
                            @endphp
                            <tr class="text-right">
                                <th colspan="5">Total Aktivitas Investasi :</th>
                                <th class="text-right">{{ $total_inv < 0 ? '('.number_format($total_inv*-1, 2, ',', '.').')' : number_format($total_inv, 2, ',', '.') }}</th>
                            </tr>

                            {{-- Aktivitas Pendanaan --}}
                            <tr>
                                <td class="h5" colspan="6">Aktivitas Pendanaan</td>
                            </tr>
                            @php
                                $total_penambahan_pend = $total_pengurangan_pend = $i = 0;
                            @endphp
                            @foreach ($data['data'] as $value)
                                @if ($value->account_code[1] == 3)
                                    @php
                                        $i++;
                                        if ($data['account']->type == 0) {
                                            $penambahan = $value->kredit;
                                            $pengurangan = $value->debit;
                                        }else {
                                            $penambahan = $value->debit;
                                            $pengurangan = $value->kredit;
                                        }
                                        $total_penambahan_pend += $penambahan;
                                        $total_pengurangan_pend += $pengurangan;
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $value->account_code }}</td>
                                        <td>{{ $value->account->name }}</td>
                                        <td class="text-right">{{ number_format($penambahan, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr class="text-right">
                                <th colspan="3">Jumlah :</th>
                                <th>{{ number_format($total_penambahan_pend, 2, ',', '.') }}</th>
                                <th>{{ number_format($total_pengurangan_pend, 2, ',', '.') }}</th>
                                <th></th>
                            </tr>
                            @php
                                $total_pend = $total_penambahan_pend - $total_pengurangan_pend;
                            @endphp
                            <tr class="text-right">
                                <th colspan="5">Total Aktivitas Pendanaan :</th>
                                <th>{{ $total_pend < 0 ? '('.number_format($total_pend*-1, 2, ',', '.').')' : number_format($total_pend, 2, ',', '.') }}</th>
                            </tr>

                            {{-- Saldo Awal --}}
                            <tr class="text-right">
                                <th colspan="5">Saldo Awal :</th>
                                <th>{{ $data['account']->beginning_balance < 0 ? '('.number_format($data['account']->beginning_balance*-1, 2, ',', '.').')' : number_format($data['account']->beginning_balance, 2, ',', '.') }}</th>
                            </tr>
                            {{-- Saldo Akhir --}}
                            @php
                                $saldo = $data['account']->beginning_balance + $total_opr + $total_inv + $total_pend;
                            @endphp
                            <tr class="text-right">
                                <th colspan="5">Saldo Akhir :</th>
                                <th>{{ $saldo < 0 ? '('.number_format($saldo*-1, 2, ',', '.').')' : number_format($saldo, 2, ',', '.') }}</th>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <table class="table card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Kelompok Akun</th>
                                <th class="text-center">Penambahan (Rp)</th>
                                <th class="text-center">Pengurangan (Rp)</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Aktivitas Operasional --}}
                            <tr>
                                <td class="h5" colspan="5">Aktivitas Operasional</td>
                            </tr>
                            @php
                                $total_penambahan_opr = $total_pengurangan_opr = $i = 0;
                            @endphp
                            @foreach ($data['group'] as $value)
                                @if (!in_array($value->account_id, [7,11,12,13,14]) && ($value->kredit != 0 || $value->debit != 0))
                                    @php
                                        $i++;
                                        if ($data['account']->type == 0) {
                                            $penambahan = $value->kredit;
                                            $pengurangan = $value->debit;
                                        }else {
                                            $penambahan = $value->debit;
                                            $pengurangan = $value->kredit;
                                        }
                                        $total_penambahan_opr += $penambahan;
                                        $total_pengurangan_opr += $pengurangan;
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $value->name }}</td>
                                        <td class="text-right">{{ number_format($penambahan, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr class="text-right">
                                <th colspan="2">Jumlah :</th>
                                <th>{{ number_format($total_penambahan_opr, 2, ',', '.') }}</th>
                                <th>{{ number_format($total_pengurangan_opr, 2, ',', '.') }}</th>
                                <th></th>
                            </tr>
                            @php
                                $total_opr = $total_penambahan_opr - $total_pengurangan_opr;
                            @endphp
                            <tr class="text-right">
                                <th colspan="4">Total Aktivitas Operasional :</th>
                                <th class="text-right">{{ $total_opr < 0 ? '('.number_format($total_opr*-1, 2, ',', '.').')' : number_format($total_opr, 2, ',', '.') }}</th>
                            </tr>
                            
                            {{-- Aktivitas Investasi --}}
                            <tr>
                                <td class="h5" colspan="5">Aktivitas Investasi</td>
                            </tr>
                            @php
                                $total_penambahan_inv = $total_pengurangan_inv = $i = 0;
                            @endphp
                            @foreach ($data['group'] as $value)
                                @if ($value->account_id == 7 && ($value->kredit != 0 || $value->debit != 0))
                                    @php
                                        $i++;
                                        if ($data['account']->type == 0) {
                                            $penambahan = $value->kredit;
                                            $pengurangan = $value->debit;
                                        }else {
                                            $penambahan = $value->debit;
                                            $pengurangan = $value->kredit;
                                        }
                                        $total_penambahan_inv += $penambahan;
                                        $total_pengurangan_inv += $pengurangan;
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $value->name }}</td>
                                        <td class="text-right">{{ number_format($penambahan, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr class="text-right">
                                <th colspan="2">Jumlah :</th>
                                <th>{{ number_format($total_penambahan_inv, 2, ',', '.') }}</th>
                                <th>{{ number_format($total_pengurangan_inv, 2, ',', '.') }}</th>
                                <th></th>
                            </tr>
                            @php
                                $total_inv = $total_penambahan_inv - $total_pengurangan_inv;
                            @endphp
                            <tr class="text-right">
                                <th colspan="4">Total Aktivitas Investasi :</th>
                                <th class="text-right">{{ $total_inv < 0 ? '('.number_format($total_inv*-1, 2, ',', '.').')' : number_format($total_inv, 2, ',', '.') }}</th>
                            </tr>

                            {{-- Aktivitas Pendanaan --}}
                            <tr>
                                <td class="h5" colspan="5">Aktivitas Pendanaan</td>
                            </tr>
                            @php
                                $total_penambahan_pend = $total_pengurangan_pend = $i = 0;
                            @endphp
                            @foreach ($data['group'] as $value)
                                @if (in_array($value->account_id, [11,12,13,14]) && ($value->kredit != 0 || $value->debit != 0))
                                    @php
                                        $i++;
                                        if ($data['account']->type == 0) {
                                            $penambahan = $value->kredit;
                                            $pengurangan = $value->debit;
                                        }else {
                                            $penambahan = $value->debit;
                                            $pengurangan = $value->kredit;
                                        }
                                        $total_penambahan_pend += $penambahan;
                                        $total_pengurangan_pend += $pengurangan;
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $value->name }}</td>
                                        <td class="text-right">{{ number_format($penambahan, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr class="text-right">
                                <th colspan="2">Jumlah :</th>
                                <th>{{ number_format($total_penambahan_pend, 2, ',', '.') }}</th>
                                <th>{{ number_format($total_pengurangan_pend, 2, ',', '.') }}</th>
                                <th></th>
                            </tr>
                            @php
                                $total_pend = $total_penambahan_pend - $total_pengurangan_pend;
                            @endphp
                            <tr class="text-right">
                                <th colspan="4">Total Aktivitas Pendanaan :</th>
                                <th>{{ $total_pend < 0 ? '('.number_format($total_pend*-1, 2, ',', '.').')' : number_format($total_pend, 2, ',', '.') }}</th>
                            </tr>

                            {{-- Saldo Awal --}}
                            <tr class="text-right">
                                <th colspan="4">Saldo Awal :</th>
                                <th>{{ $data['account']->beginning_balance < 0 ? '('.number_format($data['account']->beginning_balance*-1, 2, ',', '.').')' : number_format($data['account']->beginning_balance, 2, ',', '.') }}</th>
                            </tr>
                            {{-- Saldo Akhir --}}
                            @php
                                $saldo = $data['account']->beginning_balance + $total_opr + $total_inv + $total_pend;
                            @endphp
                            <tr class="text-right">
                                <th colspan="4">Saldo Akhir :</th>
                                <th>{{ $saldo < 0 ? '('.number_format($saldo*-1, 2, ',', '.').')' : number_format($saldo, 2, ',', '.') }}</th>
                            </tr>
                        </tbody>
                    </table>
                @endif
                
            </div>
        </div>
    </div>
</div>
@endsection