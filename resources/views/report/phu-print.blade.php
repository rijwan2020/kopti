<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Perhitungan Hasil Usaha</title>
</head>
<body class="px-5" style="font-size: 12pt;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Penjelasan Perhitungan Hasil Usaha</h2>
            <h5 class="mb-2">Per {{ date('d M Y', strtotime($data['end_date'])) }}</h5>
        </div>
    </div>

    <table class="table table-borderless">
        <tbody>
            <tr>
                <th width="5%">I.</th>
                <th>Penjualan Barang</th>
            </tr>
            <tr>
                <td class="py-0"></td>
                <td class="py-0"># Penjualan Pada Anggota</td>
            </tr>
            @php
                $total_penjualan_anggota = 0;
            @endphp
            @foreach ($data['penjualan_anggota'] as $item)
                @if ($item->saldo_penyesuaian != 0)
                    @php
                        if($item->type == 1){
                            $total_penjualan_anggota += $item->saldo_penyesuaian;
                            $saldo = $item->saldo_penyesuaian;
                        }else{
                            $total_penjualan_anggota -= $item->saldo_penyesuaian;
                            $saldo = $item->saldo_penyesuaian * -1;
                        }
                    @endphp
                    <tr>
                        <td class="py-0"></td>
                        <td class="py-0">- {{ $item->name }}</td>
                        <td class="py-0 text-right">{{ $saldo >= 0 ? 'Rp'.number_format($saldo, 2, ',', '.') : '(Rp.'.number_format($saldo *-1, 2, ',', '.').')' }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td></td>
                <td class="py-0" colspan="2">Total Penjualan Anggota</td>
                <td class="py-0 text-right">{{ $total_penjualan_anggota >= 0 ? 'Rp'.number_format($total_penjualan_anggota, 2, ',', '.') : '(Rp.'.number_format($total_penjualan_anggota *-1, 2, ',', '.').')' }}</td>
            </tr>
            <tr>
                <td class="pb-0"></td>
                <td class="pb-0"># Penjualan Pada Non Anggota</td>
            </tr>
            @php
                $total_penjualan_non_anggota = 0;
            @endphp
            @foreach ($data['penjualan_non_anggota'] as $item)
                @if ($item->saldo_penyesuaian != 0)
                    @php
                        if($item->type == 1){
                            $total_penjualan_non_anggota += $item->saldo_penyesuaian;
                            $saldo = $item->saldo_penyesuaian;
                        }else{
                            $total_penjualan_non_anggota -= $item->saldo_penyesuaian;
                            $saldo = $item->saldo_penyesuaian * -1;
                        }
                    @endphp
                    <tr>
                        <td class="py-0"></td>
                        <td class="py-0">- {{ $item->name }}</td>
                        <td class="py-0 text-right">{{ $saldo >= 0 ? 'Rp'.number_format($saldo, 2, ',', '.') : '(Rp.'.number_format($saldo *-1, 2, ',', '.').')' }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td></td>
                <td class="py-0" colspan="2">Total Penjualan Non Anggota</td>
                <td class="py-0 text-right">{{ $total_penjualan_non_anggota >= 0 ? 'Rp'.number_format($total_penjualan_non_anggota, 2, ',', '.') : '(Rp.'.number_format($total_penjualan_non_anggota *-1, 2, ',', '.').')' }}</td>
            </tr>
            @php
                $total_penjualan = $total_penjualan_anggota + $total_penjualan_non_anggota;
            @endphp
            <tr>
                <td></td>
                <td colspan="3"># Jumlah Penjualan</td>
                <td class=" text-right">{{ $total_penjualan >= 0 ? 'Rp'.number_format($total_penjualan, 2, ',', '.') : '(Rp.'.number_format($total_penjualan *-1, 2, ',', '.').')' }}</td>
            </tr>
            <tr>
                <th width="5%">II.</th>
                <th>Harga Pokok</th>
            </tr>
            <tr>
                <td class="py-0"></td>
                <td class="py-0"># Persediaan Awal</td>
                <td class="text-right py-0">{{ $data['persediaan_awal'] >= 0 ? 'Rp'.number_format($data['persediaan_awal'], 2, ',', '.') : '(Rp.'.number_format($data['persediaan_awal'] *-1, 2, ',', '.').')' }}</td>
            </tr>
            <tr>
                <td class="py-0"></td>
                <td class="py-0"># Harga Pokok Penjualan :</td>
            </tr>
            @if ($data['pembelian_kedelai'] != 0)
                <tr>
                    <td class="py-0"></td>
                    <td class="py-0">- Pembelian Kedele</td>
                    <td class="text-right py-0">{{ $data['pembelian_kedelai'] >= 0 ? 'Rp'.number_format($data['pembelian_kedelai'], 2, ',', '.') : '(Rp.'.number_format($data['pembelian_kedelai'] *-1, 2, ',', '.').')' }}</td>
                </tr>
            @endif
            @if ($data['susut_kedelai'] != 0)
                <tr>
                    <td class="py-0"></td>
                    <td class="py-0">- Susut Kedele</td>
                    <td class="text-right py-0">{{ $data['susut_kedelai'] >= 0 ? 'Rp'.number_format($data['susut_kedelai'], 2, ',', '.') : '(Rp.'.number_format($data['susut_kedelai'] *-1, 2, ',', '.').')' }}</td>
                </tr>
            @endif
            @if ($data['retur_pembelian'] != 0)
                <tr>
                    <td class="py-0"></td>
                    <td class="py-0">- Retur Pembelian</td>
                    <td class="text-right py-0">{{ $data['retur_pembelian'] >= 0 ? 'Rp'.number_format($data['retur_pembelian'], 2, ',', '.') : '(Rp.'.number_format($data['retur_pembelian'] *-1, 2, ',', '.').')' }}</td>
                </tr>
            @endif
            @php
                $barang_tersedia = $data['persediaan_awal'] + $data['pembelian_kedelai'] - $data['susut_kedelai'] - $data['retur_pembelian'];
            @endphp
            <tr>
                <td class="py-0"></td>
                <td class="py-0" colspan="2"># Barang Yang Tersedia</td>
                <td class="text-right py-0">{{ $barang_tersedia >= 0 ? 'Rp'.number_format($barang_tersedia, 2, ',', '.') : '(Rp.'.number_format($barang_tersedia *-1, 2, ',', '.').')' }}</td>
            </tr>
            <tr>
                <td class="py-0"></td>
                <td class="py-0" colspan="2"># Persediaan Akhir</td>
                <td class="text-right py-0">{{ $data['persediaan_akhir'] >= 0 ? 'Rp'.number_format($data['persediaan_akhir'], 2, ',', '.') : '(Rp.'.number_format($data['persediaan_akhir'] *-1, 2, ',', '.').')' }}</td>
            </tr>
            @php
                $hpp = $barang_tersedia - $data['persediaan_akhir'];
            @endphp
            <tr>
                <td class="py-0"></td>
                <td class="py-0" colspan="3"># Harga Pokok Penjualan</td>
                <td class="text-right py-0">{{ $hpp >= 0 ? 'Rp'.number_format($hpp, 2, ',', '.') : '(Rp.'.number_format($hpp *-1, 2, ',', '.').')' }}</td>
            </tr>
            @php
                $laba_bruto = $total_penjualan - $hpp;
            @endphp
            <tr>
                <th width="5%">III.</th>
                <th colspan="3">Laba Bruto</th>
                <th class="text-right">{{ $laba_bruto >= 0 ? 'Rp'.number_format($laba_bruto, 2, ',', '.') : '(Rp.'.number_format($laba_bruto *-1 , 2, ',', '.').')' }}</th>
            </tr>
            <tr>
                <th width="5%">IV.</th>
                <th>Biaya Biaya Usaha</th>
            </tr>
            @php
                $total_biaya_usaha = 0;
            @endphp
            @foreach ($data['biaya_biaya_usaha'] as $item)
                @if ($item->saldo_penyesuaian != 0)
                    @php
                        if($item->type == 0){
                            $total_biaya_usaha += $item->saldo_penyesuaian;
                            $saldo = $item->saldo_penyesuaian;
                        }else{
                            $total_biaya_usaha -= $item->saldo_penyesuaian;
                            $saldo = $item->saldo_penyesuaian * -1;
                        }
                    @endphp
                    <tr>
                        <td class="py-0"></td>
                        <td class="py-0">- {{ $item->name }}</td>
                        <td class="py-0 text-right">{{ $saldo >= 0 ? 'Rp'.number_format($saldo, 2, ',', '.') : '(Rp.'.number_format($saldo *-1, 2, ',', '.').')' }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td class="py-0"></td>
                <td class="py-0" colspan="2"># Total Biaya Usaha</td>
                <td class="py-0 text-right">{{ $total_biaya_usaha >= 0 ? 'Rp'.number_format($total_biaya_usaha, 2, ',', '.') : '(Rp.'.number_format($total_biaya_usaha *-1, 2, ',', '.').')' }}</td>
            </tr>
            @php
                $laba_usaha = $laba_bruto - $total_biaya_usaha;
            @endphp
            <tr>
                <td class="py-0"></td>
                <td class="py-0" colspan="3"># Laba Usaha</td>
                <td class="py-0 text-right">{{ $laba_usaha >= 0 ? 'Rp'.number_format($laba_usaha, 2, ',', '.') : '(Rp.'.number_format($laba_usaha *-1, 2, ',', '.').')' }}</td>
            </tr>
            <tr>
                <th width="5%">V.</th>
                <th>Pendapatan Lain Lain</th>
            </tr>
            @php
                $total_pendapatan_lain_lain = 0;
            @endphp
            @foreach ($data['pendapatan_lain_lain'] as $item)
                @if ($item->saldo_penyesuaian != 0)
                    @php
                        if($item->type == 1){
                            $total_pendapatan_lain_lain += $item->saldo_penyesuaian;
                            $saldo = $item->saldo_penyesuaian;
                        }else{
                            $total_pendapatan_lain_lain -= $item->saldo_penyesuaian;
                            $saldo = $item->saldo_penyesuaian * -1;
                        }
                    @endphp
                    <tr>
                        <td class="py-0"></td>
                        <td class="py-0">- {{ $item->name }}</td>
                        <td class="py-0 text-right">{{ $saldo >= 0 ? 'Rp'.number_format($saldo, 2, ',', '.') : '(Rp.'.number_format($saldo *-1, 2, ',', '.').')' }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td class="py-0"></td>
                <td class="py-0" colspan="2"># Total Pendapatan Lain Lain</td>
                <td class="py-0 text-right">{{ $total_pendapatan_lain_lain >= 0 ? 'Rp'.number_format($total_pendapatan_lain_lain, 2, ',', '.') : '(Rp.'.number_format($total_pendapatan_lain_lain *-1, 2, ',', '.').')' }}</td>
            </tr>
            @php
                $laba_usaha += $total_pendapatan_lain_lain;
            @endphp
            <tr>
                <td class="py-0" colspan="4"></td>
                <td class="py-0 text-right">{{ $laba_usaha >= 0 ? 'Rp'.number_format($laba_usaha, 2, ',', '.') : '(Rp.'.number_format($laba_usaha *-1, 2, ',', '.').')' }}</td>
            </tr>
            <tr>
                <th width="5%">VI.</th>
                <th>Biaya Lain Lain</th>
            </tr>
            @php
                $total_biaya_lain_lain = 0;
            @endphp
            @foreach ($data['biaya_lain_lain'] as $item)
                @if ($item->saldo_penyesuaian != 0)
                    @php
                        if($item->type == 0){
                            $total_biaya_lain_lain += $item->saldo_penyesuaian;
                            $saldo = $item->saldo_penyesuaian;
                        }else{
                            $total_biaya_lain_lain -= $item->saldo_penyesuaian;
                            $saldo = $item->saldo_penyesuaian * -1;
                        }
                    @endphp
                    <tr>
                        <td class="py-0"></td>
                        <td class="py-0">- {{ $item->name }}</td>
                        <td class="py-0 text-right">{{ $saldo >= 0 ? 'Rp'.number_format($saldo, 2, ',', '.') : '(Rp.'.number_format($saldo *-1, 2, ',', '.').')' }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td class="py-0"></td>
                <td class="py-0" colspan="2"># Total Biaya Lain Lian</td>
                <td class="py-0 text-right">{{ $total_biaya_lain_lain >= 0 ? 'Rp'.number_format($total_biaya_lain_lain, 2, ',', '.') : '(Rp.'.number_format($total_biaya_lain_lain *-1, 2, ',', '.').')' }}</td>
            </tr>
            @php
                $laba_usaha -= $total_biaya_lain_lain;
            @endphp
            <tr>
                <th width="5%"></th>
                <th colspan="3">Perhitungan Hasil Usaha s/d {{ date('d M Y', strtotime($data['end_date'])) }}</th>
                <th class="text-right">{{ $laba_usaha >= 0 ? 'Rp'.number_format($laba_usaha, 2, ',', '.') : '(Rp.'.number_format($laba_usaha *-1, 2, ',', '.').')' }}</th>
            </tr>
            <!--<tr>-->
            <!--    <th width="5%"></th>-->
            <!--    <th colspan="3">Perhitungan Hasil Usaha s/d {{ date('d M Y', strtotime('-1 day', strtotime($data['start_date']))) }}</th>-->
            <!--    <th class="text-right">{{ $data['shu_bulan_lalu'] >= 0 ? 'Rp'.number_format($data['shu_bulan_lalu'], 2, ',', '.') : '(Rp.'.number_format($data['shu_bulan_lalu'] *-1, 2, ',', '.').')' }}</th>-->
            <!--</tr>-->
            @php
                $shu = $data['shu_bulan_lalu'] + $laba_usaha;
            @endphp
            <!--<tr>-->
            <!--    <th width="5%"></th>-->
            <!--    <th colspan="3">Perhitungan Hasil Usaha Tahun {{ date('Y') }}</th>-->
            <!--    <th class="text-right">{{ $shu >= 0 ? 'Rp'.number_format($shu, 2, ',', '.') : '(Rp.'.number_format($shu *-1, 2, ',', '.').')' }}</th>-->
            <!--</tr>-->
        </tbody>
    </table>
    
    <div class="row text-center mt-3">
        <div class="col-sm-9">Mengetahui,</div>
        <div class="col-sm-3">Kuningan, {{ date('d F Y', strtotime($data['end_date'])) }}</div>
        <div class="col-sm-3">
            <div>Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['ketua'] ? $data['assignment']['ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-sm-3">
            <div>Wakil Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['wakil_ketua'] ? $data['assignment']['wakil_ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-sm-3">
            <div>Bendahara</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['bendahara'] ? $data['assignment']['bendahara'] : '_____________________' }} )</div>
        </div>
        <div class="col-sm-3">
            <div>Manager</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['manager'] ? $data['assignment']['manager'] : '_____________________' }} )</div>
        </div>
    </div>
    <script>
        window.print();
		setTimeout(window.close, 3000);
    </script>
</body>
</html>