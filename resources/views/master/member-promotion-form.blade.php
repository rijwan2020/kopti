@extends('layouts.application')
@section('module', 'Data Anggota')

@section('content')

<div class="card">
    <div class="card-header h4 text-center">Form Promosi Anggota</div>
    <form action="{{ route('memberPromotionSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="id" value="{{ $data['data']->id }}">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">
                    <div class="form-group">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Kode Non Anggota</td>
                                    <td>: {{ $data['data']->code }}</td>
                                </tr>
                                <tr>
                                    <td>Nama</td>
                                    <td>: {{ $data['data']->name }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Bergabung</td>
                                    <td>: {{ $data['data']->join_date }}</td>
                                </tr>
                                <tr>
                                    <td>Wilayah</td>
                                    <td>: {{ $data['data']->region->name }}</td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kode Anggota*</label>
                        <input type="text" class="form-control {{ $errors->has('code')?' is-invalid':'' }}" name="code" id="code" value="{{ old('code') ?? 'A-'.str_pad(config('config_apps.next_code_anggota'),4,'0',STR_PAD_LEFT) }}" required>
                        {!! $errors->first('code', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan kode anggota.</small>
                    </div>

                    <div class="form-group">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" colspan="3">Data Simpanan</th>
                                </tr>
                            </thead>
                            @if ($data['data']->deposit->count() > 0)
                                <thead>
                                    <tr>
                                        <th>No Rekening</th>
                                        <th>Jenis Simpanan</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['data']->deposit as $value)
                                        <tr>
                                            <td>{{ $value->account_number }}</td>
                                            <td>{{ $value->type->name }}</td>
                                            <td>Rp{{ number_format($value->balance, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-right">Jumlah</th>
                                        <th>Rp{{ number_format($data['data']->deposit->sum('balance'), 2, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="3">* Semua data simpanan akan dipindahkan ke rekening baru dan rekening lama saldo nya akan 0</td>
                                    </tr>
                                </tfoot>
                            @else
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="3">Tidak ada data</td>
                                    </tr>
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Save</button>
        </div>
    </form>
</div>
@endsection