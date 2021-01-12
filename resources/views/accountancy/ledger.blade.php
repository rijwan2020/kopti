@extends('layouts.application')

@section('module', 'Buku Besar')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-1">Buku Besar</h4>
                <h6 class="mb-1">{{date('d M Y', strtotime($data['start_date']))}} s/d {{date('d M Y', strtotime($data['end_date']))}}</h6>
            </div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center align-middle">#</th>
                            <th class="align-middle">Kode Akun</th>
                            <th class="align-middle">Nama Akun</th>
                            <th class="text-center">Saldo (Rp)</th>
                            <th class="text-center">Action</th>
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
                                <td class="text-center">{{$i}}</td>
                                <td>{{$value->code}}</td>
                                <td>{{$value->name}}</td>
                                <td class="text-right">{{ $value->saldo_penyesuaian >= 0 ? number_format($value->saldo_penyesuaian, 2, ',', '.') : '('.number_format($value->saldo_penyesuaian * -1, 2, ',', '.').')' }}</td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('ledgerDetail'))
                                        <a href="{{ route('ledgerDetail', ['id' => $value->id, $data['param']])}}" class="btn icon-btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Detail {{ $value->code }}">
                                            <i class="fa fa-book"></i>
                                        </a>
                                    @endif	
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection