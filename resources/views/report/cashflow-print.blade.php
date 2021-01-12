<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Arus {{ $data['account']->name }}</title>
</head>
<body>
    <div class="row mb-2">
        <div class="col-md-1 text-center">
            <img src="{{ !isset($data['data']['logo']) || $data['data']['logo']==''?asset('storage/logo.png'):asset('storage/'.$data['data']['logo']) }}" alt="" width="100">
        </div>
        <div class="col-md-10 text-center">
            <h3 class="mb-1">{{ config('koperasi.nama') }}</h3>
            <h2 class="mb-1">Arus {{ $data['account']->name }}</h2>
            <h5 class="mb-2">Periode {{ date('d M Y', strtotime($data['end_date'])) }}</h5>
        </div>
    </div>
    @if ($data['view'] == 'all')
        <table border="1" width="100%">
            <thead class="thead-light text-center">
                <tr>
                    <th>#</th>
                    <th>Kode Akun</th>
                    <th>Nama Akun</th>
                    <th>Penambahan (Rp)</th>
                    <th>Pengurangan (Rp)</th>
                    <th width="10%">Rp</th>
                </tr>
            </thead>
            <tbody>
                {{-- Aktivitas Operasional --}}
                <tr>
                    <th class="h5 px-1" colspan="6">Aktivitas Operasional</th>
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
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->account_code }}</td>
                            <td class="px-1">{{ $value->account->name }}</td>
                            <td class="text-right px-1">{{ number_format($penambahan, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                <tr class="text-right">
                    <th colspan="3" class="px-1">Jumlah :</th>
                    <th class="px-1">{{ number_format($total_penambahan_opr, 2, ',', '.') }}</th>
                    <th class="px-1">{{ number_format($total_pengurangan_opr, 2, ',', '.') }}</th>
                    <th></th>
                </tr>
                @php
                    $total_opr = $total_penambahan_opr - $total_pengurangan_opr;
                @endphp
                <tr class="text-right">
                    <th colspan="5" class="px-1">Total Aktivitas Operasional :</th>
                    <th class="text-right px-1">{{ $total_opr < 0 ? '('.number_format($total_opr*-1, 2, ',', '.').')' : number_format($total_opr, 2, ',', '.') }}</th>
                </tr>
                
                {{-- Aktivitas Investasi --}}
                <tr>
                    <td class="h5 px-1" colspan="6">Aktivitas Investasi</td>
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
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->account_code }}</td>
                            <td class="px-1">{{ $value->account->name }}</td>
                            <td class="text-right px-1">{{ number_format($penambahan, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                <tr class="text-right">
                    <th colspan="3" class="px-1">Jumlah :</th>
                    <th class="px-1">{{ number_format($total_penambahan_inv, 2, ',', '.') }}</th>
                    <th class="px-1">{{ number_format($total_pengurangan_inv, 2, ',', '.') }}</th>
                    <th></th>
                </tr>
                @php
                    $total_inv = $total_penambahan_inv - $total_pengurangan_inv;
                @endphp
                <tr class="text-right">
                    <th colspan="5" class="px-1">Total Aktivitas Investasi :</th>
                    <th class="text-right px-1">{{ $total_inv < 0 ? '('.number_format($total_inv*-1, 2, ',', '.').')' : number_format($total_inv, 2, ',', '.') }}</th>
                </tr>

                {{-- Aktivitas Pendanaan --}}
                <tr>
                    <td class="h5 px-1" colspan="6">Aktivitas Pendanaan</td>
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
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->account_code }}</td>
                            <td class="px-1">{{ $value->account->name }}</td>
                            <td class="text-right px-1">{{ number_format($penambahan, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                <tr class="text-right">
                    <th colspan="3" class="px-1">Jumlah :</th>
                    <th class="px-1">{{ number_format($total_penambahan_pend, 2, ',', '.') }}</th>
                    <th class="px-1">{{ number_format($total_pengurangan_pend, 2, ',', '.') }}</th>
                    <th></th>
                </tr>
                @php
                    $total_pend = $total_penambahan_pend - $total_pengurangan_pend;
                @endphp
                <tr class="text-right">
                    <th colspan="5" class="px-1">Total Aktivitas Pendanaan :</th>
                    <th class="px-1">{{ $total_pend < 0 ? '('.number_format($total_pend*-1, 2, ',', '.').')' : number_format($total_pend, 2, ',', '.') }}</th>
                </tr>

                {{-- Saldo Awal --}}
                <tr class="text-right">
                    <th colspan="5" class="px-1">Saldo Awal :</th>
                    <th class="px-1">{{ $data['account']->beginning_balance < 0 ? '('.number_format($data['account']->beginning_balance*-1, 2, ',', '.').')' : number_format($data['account']->beginning_balance, 2, ',', '.') }}</th>
                </tr>
                {{-- Saldo Akhir --}}
                @php
                    $saldo = $data['account']->beginning_balance + $total_opr + $total_inv + $total_pend;
                @endphp
                <tr class="text-right">
                    <th colspan="5" class="px-1">Saldo Akhir :</th>
                    <th class="px-1">{{ $saldo < 0 ? '('.number_format($saldo*-1, 2, ',', '.').')' : number_format($saldo, 2, ',', '.') }}</th>
                </tr>
            </tbody>
        </table>
    @else
        <table border="1" width="100%">
            <thead class="thead-light text-center">
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
                    <td class="h5 px-1" colspan="5">Aktivitas Operasional</td>
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
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->name }}</td>
                            <td class="text-right px-1">{{ number_format($penambahan, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                <tr class="text-right">
                    <th class="px-1" colspan="2">Jumlah :</th>
                    <th class="px-1">{{ number_format($total_penambahan_opr, 2, ',', '.') }}</th>
                    <th class="px-1">{{ number_format($total_pengurangan_opr, 2, ',', '.') }}</th>
                    <th></th>
                </tr>
                @php
                    $total_opr = $total_penambahan_opr - $total_pengurangan_opr;
                @endphp
                <tr class="text-right">
                    <th class="px-1" colspan="4">Total Aktivitas Operasional :</th>
                    <th class="text-right px-1">{{ $total_opr < 0 ? '('.number_format($total_opr*-1, 2, ',', '.').')' : number_format($total_opr, 2, ',', '.') }}</th>
                </tr>
                
                {{-- Aktivitas Investasi --}}
                <tr>
                    <td class="h5 px-1" colspan="5">Aktivitas Investasi</td>
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
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->name }}</td>
                            <td class="text-right px-1">{{ number_format($penambahan, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                <tr class="text-right">
                    <th class="px-1" colspan="2">Jumlah :</th>
                    <th class="px-1">{{ number_format($total_penambahan_inv, 2, ',', '.') }}</th>
                    <th class="px-1">{{ number_format($total_pengurangan_inv, 2, ',', '.') }}</th>
                    <th></th>
                </tr>
                @php
                    $total_inv = $total_penambahan_inv - $total_pengurangan_inv;
                @endphp
                <tr class="text-right">
                    <th class="px-1" colspan="4">Total Aktivitas Investasi :</th>
                    <th class="text-right px-1">{{ $total_inv < 0 ? '('.number_format($total_inv*-1, 2, ',', '.').')' : number_format($total_inv, 2, ',', '.') }}</th>
                </tr>

                {{-- Aktivitas Pendanaan --}}
                <tr>
                    <td class="h5 px-1" colspan="5">Aktivitas Pendanaan</td>
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
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->name }}</td>
                            <td class="text-right px-1">{{ number_format($penambahan, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                <tr class="text-right">
                    <th class="px-1" colspan="2">Jumlah :</th>
                    <th class="px-1">{{ number_format($total_penambahan_pend, 2, ',', '.') }}</th>
                    <th class="px-1">{{ number_format($total_pengurangan_pend, 2, ',', '.') }}</th>
                    <th></th>
                </tr>
                @php
                    $total_pend = $total_penambahan_pend - $total_pengurangan_pend;
                @endphp
                <tr class="text-right">
                    <th class="px-1" colspan="4">Total Aktivitas Pendanaan :</th>
                    <th class="px-1">{{ $total_pend < 0 ? '('.number_format($total_pend*-1, 2, ',', '.').')' : number_format($total_pend, 2, ',', '.') }}</th>
                </tr>

                {{-- Saldo Awal --}}
                <tr class="text-right">
                    <th class="px-1" colspan="4">Saldo Awal :</th>
                    <th class="px-1">{{ $data['account']->beginning_balance < 0 ? '('.number_format($data['account']->beginning_balance*-1, 2, ',', '.').')' : number_format($data['account']->beginning_balance, 2, ',', '.') }}</th>
                </tr>
                {{-- Saldo Akhir --}}
                @php
                    $saldo = $data['account']->beginning_balance + $total_opr + $total_inv + $total_pend;
                @endphp
                <tr class="text-right">
                    <th class="px-1" colspan="4">Saldo Akhir :</th>
                    <th class="px-1">{{ $saldo < 0 ? '('.number_format($saldo*-1, 2, ',', '.').')' : number_format($saldo, 2, ',', '.') }}</th>
                </tr>
            </tbody>
        </table>
    @endif
    <div class="row text-center mt-3">
        <div class="col-md-12 text-right">
            Kuningan, {{ date('d M Y', strtotime($data['end_date'])) }}
        </div>
        <div class="col-md-12">
            Mengetahui, <br>
            {{ strtoupper('Pengurus '. config('koperasi.nama')) }}
        </div>
        <div class="col-md-3">
            <div style="margin-bottom: 100px;">Ketua</div>
            <div>(_____________________)</div>
        </div>
        <div class="col-md-3">
            <div style="margin-bottom: 100px;">Sekretaris</div>
            <div>(_____________________)</div>
        </div>
        <div class="col-md-3">
            <div style="margin-bottom: 100px;">Bendahara</div>
            <div>(_____________________)</div>
        </div>
        <div class="col-md-3">
            <div style="margin-bottom: 100px;">Manager</div>
            <div>(_____________________)</div>
        </div>
    </div>
    <script>
        window.print();
		setTimeout(window.close, 300);
    </script>
</body>
</html>