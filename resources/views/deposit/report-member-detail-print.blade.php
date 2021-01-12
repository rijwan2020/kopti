<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Rekaptulasi Simpanan Anggota</title>
    <style type="text/css" media="print">
        @page { 
            size: landscape;
        }
    </style>
</head>
<body style=" font-family:arial; font-weight:normal;">
    <div class="row mb-2">
        <div class="col-md-1 text-center">
            <img src="{{ !isset($data['data']['logo']) || $data['data']['logo']==''?asset('storage/logo.png'):asset('storage/'.$data['data']['logo']) }}" alt="" width="100">
        </div>
        <div class="col-md-10 text-center">
            <h3 class="mb-1">{{ config('koperasi.nama') }}</h3>
            <h2 class="mb-1">
                @if ($data['type_id'] != 'all')
                    @foreach ($data['jenis'] as $item)
                        @if ($item->id == $data['type_id'])
                            Rekapitulasi {{ $item->name }} Anggota
                        @endif
                    @endforeach
                @else
                    Rekapitulasi Simpanan Anggota
                @endif
            </h2>
            @if ($data['region_id']!='all')
                @foreach ($data['region'] as $item)
                    @if ($item->id == $data['region_id'])
                        <br>Wilayah {{ $item->name }}
                    @endif
                @endforeach
            @endif
            <h6 class="mb-2">{{ date('d M Y', strtotime($data['start_date'])) }} s/d {{ date('d M Y', strtotime($data['end_date'])) }}</h6>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%" style="font-size: 14px;">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="px-1">Kode Anggota</th>
                        <th class="px-1">Nama Anggota</th>
                        <th class="px-1">Saldo s/d {{ date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))) }}</th>
                        <th class="px-1">Saldo Masuk</th>
                        <th class="px-1">Saldo Keluar</th>
                        <th class="px-1">Jasa</th>
                        <th class="px-1">Total Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = $total_debit = $total_kredit = $total_saldo_awal = $total_jasa = 0;
                    @endphp
                    @foreach ($data['data'] as $value)
                        @php
                            $i++;
                            $saldo = $value['saldo_awal'] + $value['kredit'] - $value['debit'] + $value['jasa'];
                            $total_saldo_awal += $value['saldo_awal'];
                            $total_kredit += $value['kredit'];
                            $total_debit += $value['debit'];
                            $total_jasa += $value['jasa'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->code }}</td>
                            <td class="px-1">{{ $value->name }}</td>
                            <td class="text-right px-1">Rp{{ number_format($value['saldo_awal'], 2, ',', '.') }}</td>
                            <td class="text-right px-1">Rp{{ number_format($value['kredit'], 2, ',', '.') }}</td>
                            <td class="text-right px-1">Rp{{ number_format($value['debit'], 2, ',', '.') }}</td>
                            <td class="text-right px-1">Rp{{ number_format($value['jasa'], 2, ',', '.') }}</td>
                            <td class="text-right px-1">Rp{{ number_format($saldo, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-right">
                        <th class="px-1" colspan="3">Jumlah :</th>
                        <th class="px-1">Rp{{ number_format($total_saldo_awal, 2, ',', '.') }}</th>
                        <th class="px-1">Rp{{ number_format($total_kredit, 2, ',', '.') }}</th>
                        <th class="px-1">Rp{{ number_format($total_debit, 2, ',', '.') }}</th>
                        <th class="px-1">Rp{{ number_format($total_jasa, 2, ',', '.') }}</th>
                        <th class="px-1">Rp{{ number_format($total_saldo_awal + $total_kredit - $total_debit + $total_jasa, 2, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <script>
		setTimeout(window.close, 300);
        window.print();
    </script>
</body>
</html>