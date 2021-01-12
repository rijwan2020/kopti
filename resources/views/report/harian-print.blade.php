<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Laporan Harian</title>
</head>
<body class="px-5" style="font-size: 12pt;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Laporan Harian</h2>
            <h5 class="mb-2">Tanggal {{ date('d-m-Y', strtotime($data['date'])) }}</h5>
        </div>
    </div>
    <table width="100%" border="1">
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
                <th colspan="5" class="px-1">Adm Keuangan</th>
            </tr>
            @foreach ($data['adm_keuangan'] as $value)
                <tr>
                    <td class="text-center">-</td>
                    <td class="px-1">{{ $value['name'] }}</td>
                    <td class="text-right px-1">{{ number_format($value['saldo_awal'], 2, ',', '.') }}</td>
                    <td class="text-right px-1">{{ number_format($value['penambahan'], 2, ',', '.') }}</td>
                    <td class="text-right px-1">{{ number_format($value['pengurangan'], 2, ',', '.') }}</td>
                    <td class="text-right px-1">{{ number_format($value['saldo_akhir'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="text-right">
                <th class="px-1" colspan="2">Jumlah :</th>
                <th class="px-1">{{ number_format($data['adm_keuangan']->sum('saldo_awal'), 2, ',', '.') }}</th>
                <th class="px-1">{{ number_format($data['adm_keuangan']->sum('penambahan'), 2, ',', '.') }}</th>
                <th class="px-1">{{ number_format($data['adm_keuangan']->sum('pengurangan'), 2, ',', '.') }}</th>
                <th class="px-1">{{ number_format($data['adm_keuangan']->sum('saldo_akhir'), 2, ',', '.') }}</th>
            </tr>
            {{-- Kewajiban titipan --}}
            <tr>
                <th class="text-center">II</th>
                <th class="px-1" colspan="5">Kewajiban Titipan</th>
            </tr>
            @foreach ($data['kewajiban_titipan'] as $value)
                <tr>
                    <td class="text-center">-</td>
                    <td class="px-1">{{ $value['name'] }}</td>
                    <td class="text-right px-1">{{ number_format($value['saldo_awal'], 2, ',', '.') }}</td>
                    <td class="text-right px-1">{{ number_format($value['penambahan'], 2, ',', '.') }}</td>
                    <td class="text-right px-1">{{ number_format($value['pengurangan'], 2, ',', '.') }}</td>
                    <td class="text-right px-1">{{ number_format($value['saldo_akhir'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="text-right">
                <th class="px-1" colspan="2">Jumlah :</th>
                <th class="px-1">{{ number_format($data['kewajiban_titipan']->sum('saldo_awal'), 2, ',', '.') }}</th>
                <th class="px-1">{{ number_format($data['kewajiban_titipan']->sum('penambahan'), 2, ',', '.') }}</th>
                <th class="px-1">{{ number_format($data['kewajiban_titipan']->sum('pengurangan'), 2, ',', '.') }}</th>
                <th class="px-1">{{ number_format($data['kewajiban_titipan']->sum('saldo_akhir'), 2, ',', '.') }}</th>
            </tr>
            {{-- Aktiva titipan --}}
            <tr>
                <th class="text-center">III</th>
                <th class="px-1" colspan="5">Aktiva Titipan</th>
            </tr>
            @foreach ($data['aktiva_titipan'] as $value)
                <tr>
                    <td class="text-center">-</td>
                    <td class="px-1">{{ $value['name'] }}</td>
                    <td class="text-right px-1">{{ number_format($value['saldo_awal'], 2, ',', '.') }}</td>
                    <td class="text-right px-1">{{ number_format($value['penambahan'], 2, ',', '.') }}</td>
                    <td class="text-right px-1">{{ number_format($value['pengurangan'], 2, ',', '.') }}</td>
                    <td class="text-right px-1">{{ number_format($value['saldo_akhir'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="text-right">
                <th class="px-1" colspan="2">Jumlah :</th>
                <th class="px-1">{{ number_format($data['aktiva_titipan']->sum('saldo_awal'), 2, ',', '.') }}</th>
                <th class="px-1">{{ number_format($data['aktiva_titipan']->sum('penambahan'), 2, ',', '.') }}</th>
                <th class="px-1">{{ number_format($data['aktiva_titipan']->sum('pengurangan'), 2, ',', '.') }}</th>
                <th class="px-1">{{ number_format($data['aktiva_titipan']->sum('saldo_akhir'), 2, ',', '.') }}</th>
            </tr>
            {{-- Persediaan Barang Jenis Kedelai --}}
            <tr>
                <th class="text-center">IV</th>
                <th class="px-1" colspan="5">Persediaan Barang Jenis Kedelai</th>
            </tr>
            @foreach ($data['persediaan'] as $value)
                <tr>
                    <td class="text-center">-</td>
                    <td class="px-1">{{ $value['name'] }}</td>
                    <td class="text-right px-1">{{ number_format($value['saldo_awal'], 2, ',', '.') }} Kg</td>
                    <td class="text-right px-1">{{ number_format($value['penambahan'], 2, ',', '.') }} Kg</td>
                    <td class="text-right px-1">{{ number_format($value['pengurangan'], 2, ',', '.') }} Kg</td>
                    <td class="text-right px-1">{{ number_format($value['saldo_akhir'], 2, ',', '.') }} Kg</td>
                </tr>
            @endforeach
            <tr class="text-right">
                <th class="px-1" colspan="2">Jumlah :</th>
                <th class="px-1">{{ number_format($data['persediaan']->sum('saldo_awal'), 2, ',', '.') }} Kg</th>
                <th class="px-1">{{ number_format($data['persediaan']->sum('penambahan'), 2, ',', '.') }} Kg</th>
                <th class="px-1">{{ number_format($data['persediaan']->sum('pengurangan'), 2, ',', '.') }} Kg</th>
                <th class="px-1">{{ number_format($data['persediaan']->sum('saldo_akhir'), 2, ',', '.') }} Kg</th>
            </tr>
            {{-- Sekretariat --}}
            <tr>
                <th class="text-center">V</th>
                <th class="px-1" colspan="5">Sekretariat</th>
            </tr>
            <tr>
                <td class="text-center">-</td>
                <td class="px-1">Surat Masuk</td>
                <td class="text-right px-1">Bh</td>
                <td class="text-right px-1">Bh</td>
                <td class="text-right px-1">Bh</td>
                <td class="text-right px-1">Bh</td>
            </tr>
            <tr>
                <td class="text-center">-</td>
                <td class="px-1">Surat Keluar</td>
                <td class="text-right px-1">Bh</td>
                <td class="text-right px-1">Bh</td>
                <td class="text-right px-1">Bh</td>
                <td class="text-right px-1">Bh</td>
            </tr>
            <tr>
                <td class="text-center">-</td>
                <td class="px-1">Anggota Penuh</td>
                <td class="text-right px-1">Org</td>
                <td class="text-right px-1">Org</td>
                <td class="text-right px-1">Org</td>
                <td class="text-right px-1">Org</td>
            </tr>
        </tbody>
    </table>
    <div class="row text-center mt-3">
        <div class="col-md-8">Mengetahui,</div>
        <div class="col-md-4">Kuningan, {{ date('d F Y', strtotime($data['date'])) }}</div>
        <div class="col-md-4">
            <div>Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['ketua'] ? $data['assignment']['ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-4">
            <div>Bendahara</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['bendahara'] ? $data['assignment']['bendahara'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-4">
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