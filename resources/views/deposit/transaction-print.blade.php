<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Data Transaksi Simpanan</title>
</head>
<body style="font-size: 10pt; font-family: Arial, sans-serif; font-weight: normal" class="px-5">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center" style="text-align: center">
            <h3 class="mb-1">{{ config('koperasi.nama') }}</h3>
            <h2 class="mb-1">Bukti Transaksi Simpanan</h2>
        </div>
    </div>
    <div class="row" style="border-top: 1px solid black">
        <div class="col-md-12">
            <table style="width: 100%">
                <tbody>
                    <tr>
                        <td class="px-1" style="width: 50%">No Referensi</td>
                        <td class="px-1">: {{ $data['data']->reference_number }}</td>
                    </tr>
                    <tr>
                        <td class="px-1" style="width: 50%">Tanggal Transaksi</td>
                        <td class="px-1">: {{ $data['data']->transaction_date }}</td>
                    </tr>
                    <tr>
                        <td class="px-1" style="width: 50%">Kode Anggota</td>
                        <td class="px-1">: {{ $data['data']->member->code }}</td>
                    </tr>
                    <tr>
                        <td class="px-1" style="width: 50%">Nama Anggota</td>
                        <td class="px-1">: {{ $data['data']->member->name }}</td>
                    </tr>
                    <tr>
                        <td class="px-1" style="width: 50%">Wilayah</td>
                        <td class="px-1">: {{ $data['data']->region->name }}</td>
                    </tr>
                    <tr>
                        <td class="px-1" style="width: 50%">No Rekening</td>
                        <td class="px-1">: {{ $data['data']->deposit->account_number }}</td>
                    </tr>
                    <tr>
                        <td class="px-1" style="width: 50%">Jenis Simpanan</td>
                        <td class="px-1">: {{ $data['data']->depositType->name }}</td>
                    </tr>
                    <tr>
                        <td class="px-1" style="width: 50%">Jenis Transaksi</td>
                        <td class="px-1">: {{ $data['type_transaction'][$data['data']->type] }}</td>
                    </tr>
                    <tr>
                        <td class="px-1" style="width: 50%">Jumlah</td>
                        <td class="px-1">: Rp{{ $data['data']->kredit > 0 ? number_format($data['data']->kredit, 2, ',', '.') : number_format($data['data']->debit, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="px-1" style="width: 50%">Keterangan</td>
                        <td class="px-1">: {{ $data['data']->note }}</td>
                    </tr>
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