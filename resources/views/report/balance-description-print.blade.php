<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Print Penjelasan Neraca</title>
    <style type="text/css" media="print">
        .dot-div {
            border-bottom:thin dashed gray; 
            width:100%; height:14px
        }
        .text-div {
            margin-top:-14px
        }
        .text-span { 
            background:#fff; 
            padding-right:5px
        }
        .pull-right {
            float:right; 
            padding-left:5px
        } 
    </style>
</head>

<body class="px-5" style="font-size: 12pt">
    @include('layouts.header-print')
    <div class="row mb-2">
        <div class="col-md-12 text-center">
            <h2 class="mb-1">Penjelasan Neraca</h2>
            <h5 class="mb-2">Periode {{ date('d M Y', strtotime($data['end_date'])) }}</h5>
        </div>
    </div>
    <div class="row" style="font-size: 12pt">
        <div class="col-md-12">
            <div class="table-wrapper">
                <table class="table table-borderless">
                    <tr>
                        <td class="h5" width="5%">I.</td>
                        <td class="h5">Aktiva Lancar</td>
                    </tr>
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($data['group'] as $value)
                        @if ($value->account_id == 6 && $value->saldo !=0)
                            @php
                                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                                $i++;
                                if ($value->type == 0) {
                                    $saldo = $value->saldo;
                                }else{
                                    $saldo = $value->saldo * -1;
                                }
                            @endphp
                            <tr>
                                <td class="py-0">1.{{ $i }}</td>
                                <td class="py-0">
                                    <div class="dot-div"></div>
                                    <div class="text-div">
                                        <span class="text-span">{{ $value->name }}</span>
                                        <span class="text-span pull-right">Rp</span>
                                    </div>
                                </td>
                                <td class="py-0 text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">{{ $deskripsi }}</td>
                                <td class="py-0"></td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">
                                    <table class="table table-borderless">
                                        <tbody>
                                            @foreach ($data['data'] as $item)
                                                @if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0)
                                                    <tr>
                                                        <td width="3%" class="py-0">-</td>
                                                        <td class="py-0" width="80%">
                                                            <div class="dot-div"></div>
                                                            <div class="text-div">
                                                                <span class="text-span">{{ $item->name }}</span>
                                                                <span class="text-span pull-right">Rp</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-right py-0">{{ $item->saldo_penyesuaian >= 0 ? number_format($item->saldo_penyesuaian,2,',','.') : '('.number_format($item->saldo_penyesuaian*-1,2,',','.').')' }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td class="h5" width="5%">II.</td>
                        <td class="h5">Investasi</td>
                    </tr>
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($data['group'] as $value)
                        @if ($value->account_id == 7 && $value->saldo !=0)
                            @php
                                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                                $i++;
                                if ($value->type == 0) {
                                    $saldo = $value->saldo;
                                }else{
                                    $saldo = $value->saldo * -1;
                                }
                            @endphp
                            <tr>
                                <td class="py-0">1.{{ $i }}</td>
                                <td class="py-0">
                                    <div class="dot-div"></div>
                                    <div class="text-div">
                                        <span class="text-span">{{ $value->name }}</span>
                                        <span class="text-span pull-right">Rp</span>
                                    </div>
                                </td>
                                <td class="py-0 text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">{{ $deskripsi }}</td>
                                <td class="py-0"></td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">
                                    <table class="table table-borderless">
                                        <tbody>
                                            @foreach ($data['data'] as $item)
                                                @if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0)
                                                    <tr>
                                                        <td width="3%" class="py-0">-</td>
                                                        <td class="py-0" width="80%">
                                                            <div class="dot-div"></div>
                                                            <div class="text-div">
                                                                <span class="text-span">{{ $item->name }}</span>
                                                                <span class="text-span pull-right">Rp</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-right py-0">{{ $item->saldo_penyesuaian >= 0 ? number_format($item->saldo_penyesuaian,2,',','.') : '('.number_format($item->saldo_penyesuaian*-1,2,',','.').')' }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td class="h5" width="5%">III.</td>
                        <td class="h5">Aktiva Tetap</td>
                    </tr>
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($data['group'] as $value)
                        @if ($value->account_id == 8 && $value->saldo !=0)
                            @php
                                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                                $i++;
                                if ($value->type == 0) {
                                    $saldo = $value->saldo;
                                }else{
                                    $saldo = $value->saldo * -1;
                                }
                            @endphp
                            <tr>
                                <td class="py-0">1.{{ $i }}</td>
                                <td class="py-0">
                                    <div class="dot-div"></div>
                                    <div class="text-div">
                                        <span class="text-span">{{ $value->name }}</span>
                                        <span class="text-span pull-right">Rp</span>
                                    </div>
                                </td>
                                <td class="py-0 text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">{{ $deskripsi }}</td>
                                <td class="py-0"></td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">
                                    <table class="table table-borderless">
                                        <tbody>
                                            @foreach ($data['data'] as $item)
                                                @if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0)
                                                    <tr>
                                                        <td width="3%" class="py-0">-</td>
                                                        <td class="py-0" width="80%">
                                                            <div class="dot-div"></div>
                                                            <div class="text-div">
                                                                <span class="text-span">{{ $item->name }}</span>
                                                                <span class="text-span pull-right">Rp</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-right py-0">{{ $item->saldo_penyesuaian >= 0 ? number_format($item->saldo_penyesuaian,2,',','.') : '('.number_format($item->saldo_penyesuaian*-1,2,',','.').')' }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td class="h5" width="5%">IV.</td>
                        <td class="h5">Kewajiban Jangka Pendek</td>
                    </tr>
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($data['group'] as $value)
                        @if ($value->account_id == 9 && $value->saldo !=0)
                            @php
                                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                                $i++;
                                if ($value->type == 1) {
                                    $saldo = $value->saldo;
                                }else{
                                    $saldo = $value->saldo * -1;
                                }
                            @endphp
                            <tr>
                                <td class="py-0">1.{{ $i }}</td>
                                <td class="py-0">
                                    <div class="dot-div"></div>
                                    <div class="text-div">
                                        <span class="text-span">{{ $value->name }}</span>
                                        <span class="text-span pull-right">Rp</span>
                                    </div>
                                </td>
                                <td class="py-0 text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">{{ $deskripsi }}</td>
                                <td class="py-0"></td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">
                                    <table class="table table-borderless">
                                        <tbody>
                                            @foreach ($data['data'] as $item)
                                                @if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0)
                                                    <tr>
                                                        <td width="3%" class="py-0">-</td>
                                                        <td class="py-0" width="80%">
                                                            <div class="dot-div"></div>
                                                            <div class="text-div">
                                                                <span class="text-span">{{ $item->name }}</span>
                                                                <span class="text-span pull-right">Rp</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-right py-0">{{ $item->saldo_penyesuaian >= 0 ? number_format($item->saldo_penyesuaian,2,',','.') : '('.number_format($item->saldo_penyesuaian*-1,2,',','.').')' }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td class="h5" width="5%">V.</td>
                        <td class="h5">Kewajiban Jangka Panjang</td>
                    </tr>
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($data['group'] as $value)
                        @if ($value->account_id == 10 && $value->saldo !=0)
                            @php
                                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                                $i++;
                                if ($value->type == 1) {
                                    $saldo = $value->saldo;
                                }else{
                                    $saldo = $value->saldo * -1;
                                }
                            @endphp
                            <tr>
                                <td class="py-0">1.{{ $i }}</td>
                                <td class="py-0">
                                    <div class="dot-div"></div>
                                    <div class="text-div">
                                        <span class="text-span">{{ $value->name }}</span>
                                        <span class="text-span pull-right">Rp</span>
                                    </div>
                                </td>
                                <td class="py-0 text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">{{ $deskripsi }}</td>
                                <td class="py-0"></td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">
                                    <table class="table table-borderless">
                                        <tbody>
                                            @foreach ($data['data'] as $item)
                                                @if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0)
                                                    <tr>
                                                        <td width="3%" class="py-0">-</td>
                                                        <td class="py-0" width="80%">
                                                            <div class="dot-div"></div>
                                                            <div class="text-div">
                                                                <span class="text-span">{{ $item->name }}</span>
                                                                <span class="text-span pull-right">Rp</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-right py-0">{{ $item->saldo_penyesuaian >= 0 ? number_format($item->saldo_penyesuaian,2,',','.') : '('.number_format($item->saldo_penyesuaian*-1,2,',','.').')' }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td class="h5" width="5%">VI.</td>
                        <td class="h5">Modal</td>
                    </tr>
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($data['group'] as $value)
                        @if (in_array($value->account_id, [11,12,13]) && $value->saldo !=0)
                            @php
                                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                                $i++;
                                if ($value->type == 1) {
                                    $saldo = $value->saldo;
                                }else{
                                    $saldo = $value->saldo * -1;
                                }
                            @endphp
                            <tr>
                                <td class="py-0">1.{{ $i }}</td>
                                <td class="py-0">
                                    <div class="dot-div"></div>
                                    <div class="text-div">
                                        <span class="text-span">{{ $value->name }}</span>
                                        <span class="text-span pull-right">Rp</span>
                                    </div>
                                </td>
                                <td class="py-0 text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">{{ $deskripsi }}</td>
                                <td class="py-0"></td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">
                                    <table class="table table-borderless">
                                        <tbody>
                                            @foreach ($data['data'] as $item)
                                                @if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0)
                                                    <tr>
                                                        <td width="3%" class="py-0">-</td>
                                                        <td class="py-0" width="80%">
                                                            <div class="dot-div"></div>
                                                            <div class="text-div">
                                                                <span class="text-span">{{ $item->name }}</span>
                                                                <span class="text-span pull-right">Rp</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-right py-0">{{ $item->saldo_penyesuaian >= 0 ? number_format($item->saldo_penyesuaian,2,',','.') : '('.number_format($item->saldo_penyesuaian*-1,2,',','.').')' }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td class="h5" width="5%">VII.</td>
                        <td class="h5">PHU</td>
                    </tr>
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($data['group'] as $value)
                        @if ($value->account_id == 14 && $value->saldo !=0)
                            @php
                                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                                $i++;
                                if ($value->type == 1) {
                                    $saldo = $value->saldo;
                                }else{
                                    $saldo = $value->saldo * -1;
                                }
                            @endphp
                            <tr>
                                <td class="py-0">1.{{ $i }}</td>
                                <td class="py-0">
                                    <div class="dot-div"></div>
                                    <div class="text-div">
                                        <span class="text-span">{{ $value->name }}</span>
                                        <span class="text-span pull-right">Rp</span>
                                    </div>
                                </td>
                                <td class="py-0 text-right">{{ $saldo >= 0 ? number_format($saldo,2,',','.') : '('.number_format($saldo*-1,2,',','.').')' }}</td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">{{ $deskripsi }}</td>
                                <td class="py-0"></td>
                            </tr>
                            <tr>
                                <td class="py-0"></td>
                                <td class="py-0">
                                    <table class="table table-borderless">
                                        <tbody>
                                            @foreach ($data['data'] as $item)
                                                @if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0)
                                                    <tr>
                                                        <td width="3%" class="py-0">-</td>
                                                        <td class="py-0" width="80%">
                                                            <div class="dot-div"></div>
                                                            <div class="text-div">
                                                                <span class="text-span">{{ $item->name }}</span>
                                                                <span class="text-span pull-right">Rp</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-right py-0">{{ $item->saldo_penyesuaian >= 0 ? number_format($item->saldo_penyesuaian,2,',','.') : '('.number_format($item->saldo_penyesuaian*-1,2,',','.').')' }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    <div class="row text-center mt-3">
        <div class="col-md-9">Mengetahui,</div>
        <div class="col-md-3">Kuningan, {{ date('d F Y', strtotime($data['end_date'])) }}</div>
        <div class="col-md-3">
            <div>Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['ketua'] ? $data['assignment']['ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-3">
            <div>Wakil Ketua</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['wakil_ketua'] ? $data['assignment']['wakil_ketua'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-3">
            <div>Bendahara</div>
            <div style="margin-top: 100px;">( {{ $data['assignment']['bendahara'] ? $data['assignment']['bendahara'] : '_____________________' }} )</div>
        </div>
        <div class="col-md-3">
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