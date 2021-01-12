<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Data Transaksi Simpanan</title>
</head>
<body class="px-5" style="font-size: 12pt; font-family:arial; font-weight:normal;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h3 class="mb-1">Data Transaksi Simpanan</h3>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-4 h6 mb-0">
            <div class="d-flex justify-content-between">
                <div>No Rekening</div>
                <div>{{ $data['deposit']->account_number }}</div>
            </div>
            <div class="d-flex justify-content-between">
                <div>Kode Anggota</div>
                <div>{{ $data['deposit']->member->code }}</div>
            </div>
            <div class="d-flex justify-content-between">
                <div>Nama Anggota</div>
                <div>{{ $data['deposit']->member->name }}</div>
            </div>
            <div class="d-flex justify-content-between">
                <div>Jenis Simpanan</div>
                <div>{{ $data['deposit']->type->name }}</div>
            </div>
            <div class="d-flex justify-content-between">
                <div>Wilayah</div>
                <div>{{ $data['deposit']->region->name }}</div>
            </div>
        </div>
        <div class="col-md-8 text-right h3">Saldo : Rp{{ number_format($data['deposit']->balance, 2, ',', '.') }}</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>No Ref</th>
                        <th>Keterangan</th>
                        <th>Tanggal Transaksi</th>
                        <th>Jenis Transaksi</th>
                        <th>Kredit (Rp)</th>
                        <th>Debit (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = $total_debit = $total_kredit = 0;
                    @endphp
                    @foreach ($data['data'] as $value)
                        @php
                            $i++;
                            $total_debit += $value->debit;
                            $total_kredit += $value->kredit;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->reference_number }}</td>
                            <td class="px-1">{{ $value->note }}</td>
                            <td class="px-1">{{ $value->transaction_date }}</td>
                            <td class="px-1">[{{ str_pad($value->type, 2, 0, STR_PAD_LEFT) }}] - {{ $data['type_transaction'][$value->type] }}</td>
                            <td class="px-1 text-right">{{ number_format($value->kredit, 2, ',', '.') }}</td>
                            <td class="px-1 text-right">{{ number_format($value->debit, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="px-1 text-right" colspan="5">Total</th>
                        <th class="px-1 text-right">{{ number_format($total_kredit, 2, ',', '.') }}</th>
                        <th class="px-1 text-right">{{ number_format($total_debit, 2, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="row text-center mt-3">
        <div class="col-md-6">Mengetahui,</div>
        <div class="col-md-6">Kuningan, {{ date('d F Y') }}</div>
        <div class="col-md-6">
            <div>Manager</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['manager'] ? $data['assignment']['manager'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-6">
            <div>Petugas</div>
            <div style="margin-top: 100px;">( {{ auth()->user()->name }} )</div>
        </div>
    </div>
    <script>
        window.print();
		setTimeout(window.close, 3000);
    </script>
</body>
</html>