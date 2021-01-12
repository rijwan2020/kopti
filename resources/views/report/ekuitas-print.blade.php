<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Perubahan Modal</title>
</head>
<body>
    <div class="row mb-2">
        <div class="col-md-1 text-center">
            <img src="{{ !isset($data['data']['logo']) || $data['data']['logo']==''?asset('storage/logo.png'):asset('storage/'.$data['data']['logo']) }}" alt="" width="100">
        </div>
        <div class="col-md-10 text-center">
            <h3 class="mb-1">{{ config('koperasi.nama') }}</h3>
            <h2 class="mb-1">Perubahan Modal</h2>
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
                    <th>Saldo Awal (Rp)</th>
                    <th>Penambahan (Rp)</th>
                    <th>Pengurangan (Rp)</th>
                    <th>Saldo Akhir (Rp)</th>
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
                        <td class="text-center">{{ $i }}</td>
                        <td class="px-1">{{ $value['code'] }}</td>
                        <td class="px-1">{{ $value['name'] }}</td>
                        <td class="text-right px-1">{{ number_format($value['beginning_balance'], 2, ',', '.') }}</td>
                        <td class="text-right px-1">{{ number_format($penambahan, 2, ',', '.') }}</td>
                        <td class="text-right px-1">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                        <td class="text-right px-1">{{ $saldo < 0 ? '('.number_format($saldo * -1, 2, ',', '.').')' : number_format($saldo, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="text-right">
                    <th colspan="3" class="px-1">Jumlah : </th>
                    <th class="text-right px-1">{{ number_format($saldo_awal, 2, ',', '.') }}</th>
                    <th class="text-right px-1">{{ number_format($total_penambahan, 2, ',', '.') }}</th>
                    <th class="text-right px-1">{{ number_format($total_pengurangan, 2, ',', '.') }}</th>
                    <th class="text-right px-1">{{ $total_saldo < 0 ? '('.number_format($total_saldo * -1, 2, ',', '.').')' : number_format($total_saldo, 2, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    @else
        <table border="1" width="100%">
            <thead class="thead-light text-center">
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
                            <td class="px-1">{{ $i }}</td>
                            <td class="px-1">{{ $value['name'] }}</td>
                            <td class="text-right px-1">{{ number_format($value['beginning_balance'], 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($penambahan, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ number_format($pengurangan, 2, ',', '.') }}</td>
                            <td class="text-right px-1">{{ $saldo < 0 ? '('.number_format($saldo * -1, 2, ',', '.').')' : number_format($saldo, 2, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="text-right">
                    <th colspan="2" class="px-1">Jumlah : </th>
                    <th class="text-right px-1">{{ number_format($saldo_awal, 2, ',', '.') }}</th>
                    <th class="text-right px-1">{{ number_format($total_penambahan, 2, ',', '.') }}</th>
                    <th class="text-right px-1">{{ number_format($total_pengurangan, 2, ',', '.') }}</th>
                    <th class="text-right px-1">{{ $total_saldo < 0 ? '('.number_format($total_saldo * -1, 2, ',', '.').')' : number_format($total_saldo, 2, ',', '.') }}</th>
                </tr>
            </tfoot>
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