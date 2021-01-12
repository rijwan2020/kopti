@extends('layouts.application')

@section('module', 'Perubahan Modal')

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
            @if (Auth::user()->hasRule('ekuitasPrint'))
                <a href="{{ route('ekuitasPrint', ['end_date' => $data['end_date'], 'view' => $data['view']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print Perubahan Modal" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('ekuitasDownload'))
                <a href="{{ route('ekuitasDownload', ['end_date' => $data['end_date'], 'view' => $data['view']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download Perubahan Modal">
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
                <h4 class="mb-1">Perubahan Modal</h4>
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
                                <th class="text-center">Saldo Awal (Rp)</th>
                                <th class="text-center">Penambahan (Rp)</th>
                                <th class="text-center">Pengurangan (Rp)</th>
                                <th class="text-center">Saldo Akhir (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = $saldo_awal = $total_penambahan = $total_pengurangan = $total_saldo = 0
                            @endphp
                            @foreach ($data['data'] as $value)
                                @php
                                    $i++;
                                    $saldo_awal += $value['beginning_balance'];
                                    if ($value['type'] == 1) {
                                        $penambahan = $value['kredit'];
                                        $pengurangan = $value['debit'];
                                    }else{
                                        $penambahan = $value['debit'];
                                        $pengurangan = $value['kredit'];
                                    }
                                    $saldo = $value['beginning_balance'] + $penambahan - $pengurangan;
                                    $total_penambahan += $penambahan;
                                    $total_pengurangan += $pengurangan;
                                    $total_saldo += $saldo;
                                @endphp
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $value['code'] }}</td>
                                    <td>{{ $value['name'] }}</td>
                                    <td class="text-right">{{ number_format($value['beginning_balance'], 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($penambahan, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ $saldo < 0 ? '('.number_format($saldo * -1, 2, ',', '.').')' : number_format($saldo, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="text-right">
                                <th colspan="3">Jumlah : </th>
                                <th class="text-right">{{ number_format($saldo_awal, 2, ',', '.') }}</th>
                                <th class="text-right">{{ number_format($total_penambahan, 2, ',', '.') }}</th>
                                <th class="text-right">{{ number_format($total_pengurangan, 2, ',', '.') }}</th>
                                <th class="text-right">{{ $total_saldo < 0 ? '('.number_format($total_saldo * -1, 2, ',', '.').')' : number_format($total_saldo, 2, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <table class="table card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Kelompok Akun</th>
                                <th class="text-center">Saldo Awal (Rp)</th>
                                <th class="text-center">Penambahan (Rp)</th>
                                <th class="text-center">Pengurangan (Rp)</th>
                                <th class="text-center">Saldo Akhir (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = $saldo_awal = $total_penambahan = $total_pengurangan = $total_saldo = 0
                            @endphp
                            @foreach ($data['group'] as $value)
                                @if (in_array($value->account_id, [11,12,13,14]))
                                    @php
                                        $i++;
                                        $saldo_awal += $value['beginning_balance'];
                                        if ($value['type'] == 1) {
                                            $penambahan = $value['kredit'];
                                            $pengurangan = $value['debit'];
                                        }else{
                                            $penambahan = $value['debit'];
                                            $pengurangan = $value['kredit'];
                                        }
                                        $saldo = $penambahan - $pengurangan;
                                        $total_penambahan += $penambahan;
                                        $total_pengurangan += $pengurangan;
                                        $total_saldo += $saldo;
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $value['name'] }}</td>
                                        <td class="text-right">{{ number_format($value['beginning_balance'], 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($penambahan, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ $saldo < 0 ? '('.number_format($saldo * -1, 2, ',', '.').')' : number_format($saldo, 2, ',', '.') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="text-right">
                                <th colspan="2">Jumlah : </th>
                                <th class="text-right">{{ number_format($saldo_awal, 2, ',', '.') }}</th>
                                <th class="text-right">{{ number_format($total_penambahan, 2, ',', '.') }}</th>
                                <th class="text-right">{{ number_format($total_pengurangan, 2, ',', '.') }}</th>
                                <th class="text-right">{{ $total_saldo < 0 ? '('.number_format($total_saldo * -1, 2, ',', '.').')' : number_format($total_saldo, 2, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                @endif
                
            </div>
        </div>
    </div>
</div>
@endsection