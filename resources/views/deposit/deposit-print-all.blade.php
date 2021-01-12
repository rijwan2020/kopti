<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Data Simpanan</title>
</head>
<body class="px-5" style="font-size: 12pt; font-family:arial; font-weight:normal;">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h3 class="mb-1">Data Simpanan</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>Nama Anggota</th>
                        <th>Kode Anggota</th>
                        <th>No Rekening</th>
                        <th>Wilayah</th>
                        <th>Jenis Simpanan</th>
                        <th>Tanggal Registrasi</th>
                        <th>Transaksi Terakhir</th>
                        <th>Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($data['data'] as $value)
                        @php
                            $i++;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i }}</td>
                            <td class="px-1">{{ $value->member->name }}</td>
                            <td class="px-1">{{ $value->member->code }}</td>
                            <td class="px-1">{{ $value->account_number }}</td>
                            <td class="px-1">{{ $value->region->name }}</td>
                            <td class="px-1">{{ $value->type->name }}</td>
                            <td class="px-1">{{ $value->registration_date }}</td>
                            <td class="px-1">{{ $value->last_transaction }}</td>
                            <td class="px-1 text-right">{{ number_format($value->balance, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
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