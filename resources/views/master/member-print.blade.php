<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Data Anggota</title>
</head>
<body class="px-5"  style="font-size: 12pt">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h3 class="mb-1">Data Anggota</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table border="1" width="100%">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="px-1">Kode</th>
                        <th class="px-1">Nama</th>
                        <th class="px-1">Jenis Kelamin</th>
                        <th class="px-1">Wilayah</th>
                        <th class="px-1">Tanggal Bergabung</th>
                        <th class="px-1">Telepon</th>
                        <th class="px-1">Keanggotaan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($data['data'] as $item)
                        @php
                            $i++;
                        @endphp
                        <tr>
                            <td class="text-center">{{$i}}</td>
                            <td class="px-1">{{ $item->code }}</td>
                            <td class="px-1">{{ $item->name }}</td>
                            <td class="px-1">{{ $item->gender == 1 ? 'Laki-Laki' : 'Perempuan' }}</td>
                            <td class="px-1">{{ $item->region->name }}</td>
                            <td class="px-1">{{ $item->join_date }}</td>
                            <td class="px-1">{{ $item->phone }}</td>
                            <td class="px-1">{{ $item->status==0?'Non Anggota':($item->status==1?'Anggota Aktif':'Anggota Keluar') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        window.print();
		setTimeout(window.close, 3000);
    </script>
</body>
</html>