@extends('layouts.application')

@section('module', 'Simpanan')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="mb-1">Cetak Tabungan {{ $data['deposit']->account_number }}</h4>
                <div>
                    @if (Auth::user()->hasRule('depositBookReset'))
                        <a href="{{ route('depositBookResetAll', ['id' => $data['deposit']->id]) }}" class="btn btn-success" data-toggle="tooltip" data-placement="top" data-state="dark" title="Reset buku tabungan : {{ $data['deposit']->account_number }}">
                            <i class="fa fa-undo"></i> Reset
                        </a>
                    @endif
                    @if (Auth::user()->hasRule('depositBookPrint'))
                        <a href="{{ route('depositBookPrint', ['id' => $data['deposit']->id, 'page' => $data['data']->currentPage()]) }}" class="btn btn-dark" data-toggle="tooltip" data-placement="top" data-state="dark" title="Print buku tabungan : {{ $data['deposit']->account_number }}">
                            <i class="fa fa-print"></i> Print
                        </a>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Tanggal Transaksi</th>
                            <th>Kode Transaksi</th>
                            <th>Kredit (Rp)</th>
                            <th>Debit (Rp)</th>
                            <th>Saldo (Rp)</th>
                            <th>Diinput oleh</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = ($data['data']->currentPage() - 1) * $data['data']->perPage();
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                            @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $value->transaction_date }}</td>
                                <td>{{ str_pad($value->type_transaction, 2, 0, STR_PAD_LEFT) }}</td>
                                <td class="text-right">{{ number_format($value->debit, 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value->kredit, 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value->balance, 2, ',', '.') }}</td>
                                <td>{{ $value->userInput->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $value->print == 1 ? 'success' : 'danger' }}">{{ $value->print == 1 ? 'Sudah dicetak' : 'Belum Dicetak' }}</span>
                                </td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('depositBookReset'))
                                        <a href="{{ route('depositBookReset', ['deposit_id' => $data['deposit']->id, 'id' => $value->id]) }}" class="btn icon-btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Reset">
                                            <i class="fa fa-sync"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        Total Record : <strong>{{$data['data']->count() + (25*($data['data']->currentPage() - 1))}}</strong> of <strong>{{$data['data']->total()}}</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $data['data']->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection