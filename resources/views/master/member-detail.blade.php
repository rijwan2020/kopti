@extends('layouts.application')

@section('module', 'Data Anggota')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Detail Data Anggota</div>
            <div class="card overflow-hidden">
                <div class="row no-gutters row-bordered row-border-light">
                    <div class="col-md-3 pt-0">
                        <div class="list-group list-group-flush account-settings-links">
                            <a class="list-group-item list-group-item-action active" data-toggle="list" href="#info">Info</a>
                            <a class="list-group-item list-group-item-action" data-toggle="list" href="#simpanan">Data Simpanan</a>
                            <a class="list-group-item list-group-item-action" data-toggle="list" href="#transaksi">Histori Transaksi</a>
                            <a class="list-group-item list-group-item-action" href="{{ route('memberEdit', ['id'=>$data['data']->id]) }}">Edit Anggota <span class="fa fa-external-link-alt"></span></a>
                            <a class="list-group-item list-group-item-action" href="{{ route('memberActivity', ['id'=>$data['data']->id]) }}">Catatan Aktivitas <span class="fa fa-external-link-alt"></span></a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="info">
                                <div class="card-body media align-items-center justify-content-center">
                                    <a href="{{ $data['data']->image==''?asset('storage/profile.png'):asset('storage/'.$data['data']->image) }}" target="_blank">
                                        <img src="{{ $data['data']->image==''?asset('storage/profile.png'):asset('storage/'.$data['data']->image) }}" alt="" class="ui-w-120">
                                    </a>
                                </div>
                                <hr class="border-light m-0">
                                <div class="card-body">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td>Kode Anggota</td>
                                                <td>: {{ $data['data']->code }}</td>
                                            </tr>
                                            <tr>
                                                <td>Nama Anggota</td>
                                                <td>: {{ $data['data']->name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Status Keanggotaaan</td>
                                                <td>: <span class="badge badge-{{ $data['data']->status == 0 ? 'info' : ($data['data']->status==1 ? 'success' : 'danger') }}">{{ $data['data']->status == 0 ? 'Non Anggota' : ($data['data']->status==1 ? 'Anggota' : 'Keluar') }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>Jenis Kelamin</td>
                                                <td>: {{ $data['data']->gender == 1 ? 'Laki-Laki' : 'Perempuan' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tempat Lahir</td>
                                                <td>: {{ $data['data']->place_of_birth ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Lahir</td>
                                                <td>: {{ $data['data']->date_of_birth ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Agama</td>
                                                <td>: {{ $data['data']->religion ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pendidikan</td>
                                                <td>: {{ $data['data']->education ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Alamat</td>
                                                <td>: {{ $data['data']->address ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Desa / Kelurahan</td>
                                                <td>: {{ $data['data']->village->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Kecamatan</td>
                                                <td>: {{ $data['data']->district->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Kabupaten / Kota</td>
                                                <td>: {{ $data['data']->regency->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Province</td>
                                                <td>: {{ $data['data']->province->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>No Telepon</td>
                                                <td>: {{ $data['data']->phone ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Wilayah Anggota</td>
                                                <td>: {{ $data['data']->region->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pengrajin</td>
                                                <td>: {{ $data['data']->craftman ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Jatah Kedelai</td>
                                                <td>: {{ $data['data']->soybean_ration." Kg" ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Bahan Baku</td>
                                                <td>: {{ $data['data']->raw_material ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Bahan Pembantu</td>
                                                <td>: {{ $data['data']->adjuvant ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Bahan Tambahan</td>
                                                <td>: {{ $data['data']->extra_material ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Hasil Produksi</td>
                                                <td>: {{ $data['data']->production_result ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pendapatan</td>
                                                <td>: Rp{{ number_format($data['data']->income ?? '0') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pemasaran</td>
                                                <td>: {{ $data['data']->marketing ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Permodalan</td>
                                                <td>: {{ $data['data']->capital ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pengalaman</td>
                                                <td>: {{ $data['data']->experience ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Domisili</td>
                                                <td>: {{ $data['data']->domicile ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tempat Usaha</td>
                                                <td>: {{ $data['data']->place_of_business ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Alat Produksi</td>
                                                <td>: {{ $data['data']->production_tool ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Kriteria</td>
                                                <td>: {{ $data['data']->criteria ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Surat HO</td>
                                                <td>: {{ $data['data']->ho_letter == 1 ? 'Ada' : 'Tidak Ada' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Surat Izin</td>
                                                <td>: {{ $data['data']->license == 1 ? 'Ada' : 'Tidak Ada' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Surat IMB</td>
                                                <td>: {{ $data['data']->imb_letter == 1 ? 'Ada' : 'Tidak Ada' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Surat PBB</td>
                                                <td>: {{ $data['data']->pbb_letter == 1 ? 'Ada' : 'Tidak Ada' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Alat Pemadam</td>
                                                <td>: {{ $data['data']->extinguisher == 1 ? 'Ada' : 'Tidak Ada' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Bergabung</td>
                                                <td>: {{ $data['data']->join_date ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tanggungan</td>
                                                <td>: {{ $data['data']->dependent ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Jumlah Tanggungan</td>
                                                <td>: {{ $data['data']->total_dependent ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Jumlah Anak</td>
                                                <td>: {{ $data['data']->total_children ?? '-' }}</td>
                                            </tr>
                                            @if ($data['data']->status == 1)
                                                <tr>
                                                    <td>Username Aplikasi</td>
                                                    <td>: {{ $data['data']->user->username ?? '-' }}</td>
                                                </tr>
                                            @elseif($data['data']->status == 2)
                                                <tr>
                                                    <td>Tanggal Keluar</td>
                                                    <td>: {{ $data['data']->out_date ?? '-' }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="simpanan">
                                <div class="card-body pb-2">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>No Rekening</th>
                                                <th>Jenis Simpanan</th>
                                                <th>Saldo (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($data['data']->deposit->count() > 0)
                                                @php
                                                    $i = $total_simpanan = 0;
                                                @endphp
                                                @foreach ($data['data']->deposit as $value)
                                                    @php
                                                        $i++;
                                                        $total_simpanan += $value->balance
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $i }}</td>
                                                        <td>{{ $value->account_number }}</td>
                                                        <td>{{ $value->type->name }}</td>
                                                        <td class="text-right">{{ number_format($value->balance, 2, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr class="text-right">
                                                    <th colspan="3">Total Simpanan : </th>
                                                    <th>{{ number_format($total_simpanan, 2, ',', '.') }}</th>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td class="text-center" colspan="4">Tidak ada simpanan</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="transaksi">
                                <div class="card-body pb-2">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Tanggal Transaksi</th>
                                                <th>No ref</th>
                                                <th>Nama Transaksi</th>
                                                <th>Jumlah (Rp)</th>
                                            </tr>
                                            <tbody>
                                                @if ($data['data']->transaksi->count() > 0)
                                                    @php
                                                        $i = 0;
                                                    @endphp
                                                    @foreach ($data['data']->transaksi as $value)
                                                        @php
                                                            $i++;
                                                            if ($i == 21) break;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $i }}</td>
                                                            <td>{{ $value->transaction_date }}</td>
                                                            <td>{{ $value->reference_number }}</td>
                                                            <td>{{ $value->name }}</td>
                                                            <td class="text-right">{{ number_format($value->detail->sum('debit'), 2, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                    @if (Auth::user()->hasRule('memberTransaksi'))
                                                        <tr>
                                                            <td class="text-center" colspan="5">
                                                                <a href="{{ route('memberTransaksi', ['id'=>$data['data']->id]) }}">Lihat Semua</a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td class="text-center" colspan="5">Tidak ada data</td>
                                                    </tr>                                                    
                                                @endif
                                            </tbody>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
              </div>
        </div>
    </div>
</div>

</div>

@endsection