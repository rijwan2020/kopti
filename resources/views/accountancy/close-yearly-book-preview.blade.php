@extends('layouts.application')

@section('module', 'Tutup Buku')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-1">Preview Tutup Buku</h4>
                <h6 class="mb-1">Periode {{date('d M Y', strtotime($data['start_periode']))}} s/d {{date('d M Y', strtotime($data['end_periode']))}}</h6>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th>Saldo Normal</th>
                            <th>Saldo Awal ({{ date('d M Y', strtotime('-1 day', strtotime($data['start_periode']))) }}) (Rp)</th>
                            <th>Debit (Rp)</th>
                            <th>Kredit (Rp)</th>
                            <th>Saldo Akhir ({{ date('d M Y', strtotime($data['end_periode'])) }}) (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($data['data'] as $key => $value)
                            @php
                                $i++;
                            @endphp
                            <tr>
                                <td class="text-center">{{$i}}</td>
                                <td>{{$value['code']}}</td>
                                <td>{{$value['name']}}</td>
                                <td>{{$value['type'] ? 'Kredit' : 'Debit'}}</td>
                                <td class="text-right">{{ $value->saldo_awal >= 0 ? number_format($value->saldo_awal,2,',','.') : '('.number_format($value->saldo_awal*-1,2,',','.').')' }}</td>
                                <td class="text-right">{{ number_format($value->debit,2,',','.') }}</td>
                                <td class="text-right">{{ number_format($value->kredit,2,',','.') }}</td>
                                <td class="text-right">{{ $value->saldo_penyesuaian >= 0 ? number_format($value->saldo_penyesuaian,2,',','.') : '('.number_format($value->saldo_penyesuaian*-1,2,',','.').')' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="form-group">
                    <p>Tanggal Tutup Buku: <b>{{ $data['closing_date'] }}</b></p>
                    <p>Catatan: {{ $data['description'] }}</p>
                </div>
                <form action="{{ route('closeYearlyBookConfirm') }}" class="text-center" method="POST">
                    @csrf
                    <input type="hidden" name="start_periode" value="{{$data['start_periode']}}">
                    <input type="hidden" name="end_periode" value="{{$data['end_periode']}}">
                    <input type="hidden" name="closing_date" value="{{$data['closing_date']}}">
                    <input type="hidden" name="description" value="{{$data['description']}}">
                    <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection