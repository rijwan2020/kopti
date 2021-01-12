<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Catatan Aktivitas Anggota</title>
</head>
<body class="px-5">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Catatan Aktivitas Anggota</h2>
            <h5 class="mb-2">Sampai Tanggal {{ date('d-m-Y', strtotime($data['date'])) }}</h5>
        </div>
    </div>
    
    <div>
        <table class="borderless mb-3" width="100%">
            <tbody>
                <tr>
                    <td width="30%">Kode Anggota</td>
                    <td>: {{ $data['data']->code }}</td>
                </tr>
                <tr>
                    <td>Nama Anggota</td>
                    <td>: {{ $data['data']->name }}</td>
                </tr>
                <tr>
                    <td>Wilayah</td>
                    <td>: {{ $data['data']->region->name }}</td>
                </tr>
                <tr>
                    <td>Jatah Per Bulan</td>
                    <td>: {{ $data['data']->soybean_ration }}</td>
                </tr>
            </tbody>
        </table>
        <h5>Jumlah Simpanan</h5>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <table class="table table-borderless">
                    @php
                        $total_saldo = 0
                    @endphp
                    @foreach ($data['data']->deposit as $value)
                        @php
                            $saldo = $value->transaction->where('transaction_date', '<=', $data['date']." 23:59:59")->sum('kredit') - $value->transaction->where('transaction_date', '<=', $data['date']." 23:59:59")->sum('debit');
                            $total_saldo += $saldo;
                        @endphp
                        <tr>
                            <td class="py-0">- Jumlah {{ $value->type->name }}</td>
                            <td class="text-right py-0">: Rp</td>
                            <td class="text-right py-0">{{ number_format($saldo, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tfoot>
                        <tr>
                            <th>Total Simpanan</th>
                            <th class="text-right">: Rp</th>
                            <th class="text-right">{{ number_format($total_saldo, 2, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <h5>Jumlah Kredit</h5>
        <div class="col-md-8 offset-md-2">
            <table class="table table-borderless">
                @php
                    $piutang = $data['data']->saleDebt->where('tanggal_transaksi', '<=', $data['date'].' 23:59:59')->sum('total') - $data['data']->saleDebt->where('tanggal_transaksi', '<=', $data['date'].' 23:59:59')->sum('pay');
                @endphp
                <tr>
                    <td class="py-0">- Piutang Kedelai</td>
                    <td class="text-right py-0">: Rp</td>
                    <td class="text-right py-0">{{ number_format($piutang, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th width="60%">Jumlah Kredit</th>
                    <th class="text-right">: Rp</th>
                    <th class="text-right">{{ number_format($piutang, 2, ',', '.') }}</th>
                </tr>
            </table>
        </div>
    </div>
    <div class="row text-center mt-3">
        <div class="col-md-3">Mengetahui,</div>
        <div class="col-md-3 offset-md-6">Kuningan, {{ date('d F Y', strtotime($data['date'])) }}</div>
        <div class="col-md-3">
            <div>Petugas</div>
            <div style="margin-top: 100px;">( {{ auth()->user()->name }} )</div>
        </div>
        <div class="col-md-3 offset-md-6">
            <div>Anggota</div>
            <div style="margin-top: 100px;">( {{ $data['data']->name }} )</div>
        </div>
    </div>
    <script>
        window.print();
		setTimeout(window.close, 2000);
    </script>
</body>
</html>