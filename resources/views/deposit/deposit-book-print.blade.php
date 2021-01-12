<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> --}}
    <title>Print Data Simpanan</title>
</head>
<body>
    <table border="0" width="533px" style="font-size: 12px; margin-top: 40px">
        <tbody>
            @php
                $i = 0;
            @endphp
            @foreach ($data['data'] as $value)
                @php
                    $i++;
                @endphp
                @if ($i == 16)
                    <tr><td colspan="7">&nbsp;</td></tr>
                    <tr><td colspan="7">&nbsp;</td></tr>
                @endif
                @if ($value->print == 0)
                    <tr>
                        <td style="text-align: center; width: 30px;">{{ $i }}</td>
                        <td style="text-align: center; width: 60px;">{{ $value->transaction_date }}</td>
                        <td style="text-align: center; width: 30px;">{{ str_pad($value->type_transaction, 2, 0, STR_PAD_LEFT) }}</td>
                        <td style="text-align: right; width: 90px;">{{ number_format($value->debit, 2, ',', '.') }}</td>
                        <td style="text-align: right; width: 100px;">{{ number_format($value->kredit, 2, ',', '.') }}</td>
                        <td style="text-align: right; width: 100px;">{{ number_format($value->balance, 2, ',', '.') }}</td>
                        <td style="text-align: center; width: 50px;">{{ $value->created_by }}</td>
                    </tr>
                @else
                    <tr><td colspan="7">&nbsp;</td></tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <script>
        window.print();
        window.addEventListener("afterprint", function(event) {
            var a = confirm('Konfirmasi print. Jika sudah di print tekan OK!');
            if (a == true) {
                window.location = "{{ route('depositBookPrintConfirm', ['id' => $data['deposit']->id, 'page' => $data['data']->currentPage()]) }}";
            } else {
                window.location = "{{ route('depositBook', ['id' => $data['deposit']->id]) }}";
            }
        });
    </script>
</body>
</html>