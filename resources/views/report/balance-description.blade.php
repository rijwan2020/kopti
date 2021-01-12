@extends('layouts.application')

@section('module', 'Neraca')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        @if (isset($data['tbb_id']))
            <input type="hidden" name="tbb_id" value="{{ $data['tbb_id'] }}">
        @endif
        @if (isset($data['tbt_id']))
            <input type="hidden" name="tbt_id" value="{{ $data['tbt_id'] }}">
        @endif
        
        <div class="col-md-4 mb-3">
            @if (!isset($data['tbb_id']) && !isset($data['tbt_id']))
                <label class="form-label">Filter Tanggal</label>
                <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                        <input type="text" class="form-control datepicker" name="end_date" value="{{$data['end_date']}}">
                    <span class="input-group-append">
                        <button class="btn btn-secondary" type="submit">Filter</button>
                    </span>
                </div>
            @endif
        </div>

        <div class="col-md-8 text-right">
            @if (Auth::user()->hasRule('balanceDescriptionPrint'))
                <a href="{{ route('balanceDescriptionPrint', $data['param']) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print Penjelasan Neraca" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('balanceDescriptionDownload'))
                <a href="{{ route('balanceDescriptionDownload', $data['param']) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download Penjelasan Neraca">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-1">Penjelasan Neraca</h4>
                <h6 class="mb-1">Periode {{ date('d M Y', strtotime($data['end_date'])) }}</h6>
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table class="table table-borderless">
                        <tr>
                            <td class="h5" width="5%">I.</td>
                            <td class="h5" colspan="2">Aktiva Lancar</td>
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
                            <td class="h5" colspan="2">Investasi</td>
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
                            <td class="h5" colspan="2">Aktiva Tetap</td>
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
                            <td class="h5" colspan="2">Kewajiban Jangka Pendek</td>
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
                            <td class="h5" colspan="2">Kewajiban Jangka Panjang</td>
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
                            <td class="h5" colspan="2">Modal</td>
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
                            <td class="h5" colspan="2">PHU</td>
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
    </div>
</div>
@endsection

@section('styles')
    <style>
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
@endsection