@extends('layouts.application')

@section('module', 'Laporan Harian')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        <div class="col-md-4 mb-3">
            <label class="form-label">Filter Tanggal</label>
            <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                    <input type="text" class="form-control datepicker" name="date" value="{{$data['date']}}">
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Filter</button>
                </span>
            </div>
        </div>

        <div class="col-md text-right">
            @if (Auth::user()->hasRule('laporanKasBank'))
                <a href="{{ route('laporanKasBank') }}" class="btn my-1 btn-info" data-toggle="tooltip" data-state="dark" title="Laporan Pemasukan/Pengeluaran Kas dan Bank">
                    <i class="fa fa-bars"></i>
                    Laporan Kas Bank
                </a>
            @endif
            @if (Auth::user()->hasRule('laporanHarianPrint'))
                <a href="{{ route('laporanHarianPrint', ['date' => $data['date']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print Laporan Harian" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('laporanHarianDownload'))
                <a href="{{ route('laporanHarianDownload', ['date' => $data['date']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download Laporan Harian">
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
                <h4 class="mb-1">Laporan Harian</h4>
                <h6 class="mb-1">Tanggal {{ date('d-m-Y', strtotime($data['date'])) }}</h6>
            </div>
            <div class="card-wrapper table-wrapper">
                <table class="table table-bordered card-table">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>Bidang</th>
                            <th>Saldo Lalu (Rp)</th>
                            <th>Penerimaan (Rp)</th>
                            <th>Pengeluaran (Rp)</th>
                            <th>Saldo (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Adm Keuangan --}}
                        <tr>
                            <th class="text-center">I</th>
                            <th colspan="5">Adm Keuangan</th>
                        </tr>
                        @foreach ($data['adm_keuangan'] as $value)
                            <tr>
                                <td class="text-center">-</td>
                                <td>{{ $value['name'] }}</td>
                                <td class="text-right">{{ number_format($value['saldo_awal'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['penambahan'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['pengurangan'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['saldo_akhir'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="text-right">
                            <th colspan="2">Jumlah :</th>
                            <th>{{ number_format($data['adm_keuangan']->sum('saldo_awal'), 2, ',', '.') }}</th>
                            <th>{{ number_format($data['adm_keuangan']->sum('penambahan'), 2, ',', '.') }}</th>
                            <th>{{ number_format($data['adm_keuangan']->sum('pengurangan'), 2, ',', '.') }}</th>
                            <th>{{ number_format($data['adm_keuangan']->sum('saldo_akhir'), 2, ',', '.') }}</th>
                        </tr>
                        {{-- Kewajiban titipan --}}
                        <tr>
                            <th class="text-center">II</th>
                            <th colspan="5">Kewajiban Titipan</th>
                        </tr>
                        @foreach ($data['kewajiban_titipan'] as $value)
                            <tr>
                                <td class="text-center">-</td>
                                <td>{{ $value['name'] }}</td>
                                <td class="text-right">{{ number_format($value['saldo_awal'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['penambahan'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['pengurangan'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['saldo_akhir'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="text-right">
                            <th colspan="2">Jumlah :</th>
                            <th>{{ number_format($data['kewajiban_titipan']->sum('saldo_awal'), 2, ',', '.') }}</th>
                            <th>{{ number_format($data['kewajiban_titipan']->sum('penambahan'), 2, ',', '.') }}</th>
                            <th>{{ number_format($data['kewajiban_titipan']->sum('pengurangan'), 2, ',', '.') }}</th>
                            <th>{{ number_format($data['kewajiban_titipan']->sum('saldo_akhir'), 2, ',', '.') }}</th>
                        </tr>
                        {{-- Aktiva titipan --}}
                        <tr>
                            <th class="text-center">III</th>
                            <th colspan="5">Aktiva Titipan</th>
                        </tr>
                        @foreach ($data['aktiva_titipan'] as $value)
                            <tr>
                                <td class="text-center">-</td>
                                <td>{{ $value['name'] }}</td>
                                <td class="text-right">{{ number_format($value['saldo_awal'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['penambahan'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['pengurangan'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['saldo_akhir'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="text-right">
                            <th colspan="2">Jumlah :</th>
                            <th>{{ number_format($data['aktiva_titipan']->sum('saldo_awal'), 2, ',', '.') }}</th>
                            <th>{{ number_format($data['aktiva_titipan']->sum('penambahan'), 2, ',', '.') }}</th>
                            <th>{{ number_format($data['aktiva_titipan']->sum('pengurangan'), 2, ',', '.') }}</th>
                            <th>{{ number_format($data['aktiva_titipan']->sum('saldo_akhir'), 2, ',', '.') }}</th>
                        </tr>
                        {{-- Persediaan Barang Jenis Kedelai --}}
                        <tr>
                            <th class="text-center">IV</th>
                            <th colspan="5">Persediaan Barang Jenis Kedelai</th>
                        </tr>
                        @foreach ($data['persediaan'] as $value)
                            <tr>
                                <td class="text-center">-</td>
                                <td>{{ $value['name'] }}</td>
                                <td class="text-right">{{ number_format($value['saldo_awal'], 2, ',', '.') }} Kg</td>
                                <td class="text-right">{{ number_format($value['penambahan'], 2, ',', '.') }} Kg</td>
                                <td class="text-right">{{ number_format($value['pengurangan'], 2, ',', '.') }} Kg</td>
                                <td class="text-right">{{ number_format($value['saldo_akhir'], 2, ',', '.') }} Kg</td>
                            </tr>
                        @endforeach
                        <tr class="text-right">
                            <th colspan="2">Jumlah :</th>
                            <th>{{ number_format($data['persediaan']->sum('saldo_awal'), 2, ',', '.') }} Kg</th>
                            <th>{{ number_format($data['persediaan']->sum('penambahan'), 2, ',', '.') }} Kg</th>
                            <th>{{ number_format($data['persediaan']->sum('pengurangan'), 2, ',', '.') }} Kg</th>
                            <th>{{ number_format($data['persediaan']->sum('saldo_akhir'), 2, ',', '.') }} Kg</th>
                        </tr>
                        {{-- Sekretariat --}}
                        <tr>
                            <th class="text-center">V</th>
                            <th colspan="5">Sekretariat</th>
                        </tr>
                        <tr>
                            <td class="text-center">-</td>
                            <td>Surat Masuk</td>
                            <td class="text-right">Bh</td>
                            <td class="text-right">Bh</td>
                            <td class="text-right">Bh</td>
                            <td class="text-right">Bh</td>
                        </tr>
                        <tr>
                            <td class="text-center">-</td>
                            <td>Surat Keluar</td>
                            <td class="text-right">Bh</td>
                            <td class="text-right">Bh</td>
                            <td class="text-right">Bh</td>
                            <td class="text-right">Bh</td>
                        </tr>
                        <tr>
                            <td class="text-center">-</td>
                            <td>Anggota Penuh</td>
                            <td class="text-right">Org</td>
                            <td class="text-right">Org</td>
                            <td class="text-right">Org</td>
                            <td class="text-right">Org</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection