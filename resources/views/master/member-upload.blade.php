@extends('layouts.application')

@section('module', 'Data Anggota')

@section('content')
@if ($data['data']->total() > 0)
    <div class="ui-bordered px-3 pt-3 mb-3">
        <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
            <div class="col-md-1 mb-3">
                <label class="form-label">Limit</label>
                <select class="select2 form-control" name="limit">
                    <option value="25" {{$data['limit'] == 25?'selected':''}}>25</option>
                    <option value="50" {{$data['limit'] == 50?'selected':''}}>50</option>
                    <option value="100" {{$data['limit'] == 100?'selected':''}}>100</option>
                    <option value="150" {{$data['limit'] == 150?'selected':''}}>150</option>
                    <option value="200" {{$data['limit'] == 200?'selected':''}}>200</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Pencarian</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Kata Kunci" name="q" value="{{$data['q']}}">
                    <span class="input-group-append">
                        <button class="btn btn-secondary" type="submit">Cari</button>
                    </span>
                    @if (!empty($data['q']))
                        <span class="input-group-append">
                            <a class="btn btn-danger" href="{{ url()->current() }}"><i class="fa fa-times"></i></a>
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-md text-right">
                <a href="{{ route('memberUpload', ['confirm' => 1]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Konfirmasi upload">
                    <i class="fa fa-check"></i>
                    Konfirmasi
                </a>
                <a href="{{ route('memberUpload', ['confirm' => 0]) }}" class="btn my-1 btn-danger" data-toggle="tooltip" data-state="dark" title="Batalkan upload">
                    <i class="fa fa-times"></i>
                    Batalkan
                </a>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header h4 text-center">Preview Data Upload Anggota</div>
                <div class="table-responsive">
                    <table class="table card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Wilayah</th>
                                <th>Keanggotaan</th>
                                <th>Tempat Lahir</th>
                                <th>Tanggal Lahir</th>
                                <th>Jenis Kelamin</th>
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
                                    <td>{{ $value->code }}</td>
                                    <td>{{ $value->name }}</td>
                                    <td>{{ $value->region->name ?? '' }}</td>
                                    <td>{{ $value->status == 0 ? 'Non Anggota' : ($value->status==1 ? 'Anggota' : 'Keluar') }}</td>
                                    <td>{{ $value->place_of_birth }}</td>
                                    <td>{{ $value->date_of_birth }}</td>
                                    <td>{{ $value->gender == 1 ? 'Laki-Laki':'Perempuan' }}</td>
                                    <td class="text-center">
                                        <a href="#" class="btn icon-btn btn-success btn-sm preview" data-toggle="tooltip" data-placement="top" data-state="dark" title="Lihat Data Anggota"
                                        data-code="{{$value->code}}"
                                        data-name="{{$value->name}}"
                                        data-region="{{ $value->region->name ?? ''}}"
                                        >
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-3">
                            Total Record : <strong>{{$data['data']->count() + ($data['limit']*($data['data']->currentPage() - 1))}}</strong> of <strong>{{$data['data']->total()}}</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $data['data']->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-preview-anggota-keluar" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header mb-0">
					<h5 id="h5-detail-anggota-keluar" class="mb-1"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body mt-0">
					<table width="100%" class="mt-0">
						<tr>
							<td>Kode Anggota</td>
							<td class="code"></td>
						</tr>
						<tr>
							<td>Nama Anggota</td>
							<td class="name"></td>
						</tr>
						<tr>
							<td>Wilayah</td>
							<td class="region"></td>
						</tr>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
@else
    <div class="card">
        <div class="card-header h4 text-center">Form Upload Data Anggota</div>
        <form action="{{ route('memberUploadSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
            <div class="card-body">
                @csrf
                <div class="row">
                    <div class="col-xl-10 offset-xl-1">

                        <div class="form-group">
                            <label class="form-label">File Attachment</label>
                            <div class="input-group file-input">
                                <label class="custom-file">
                                    <input type="file" class="custom-file-input upload {{ $errors->has('file')?' is-invalid':'' }}" data-target="input-file" name="file">
                                    <span class="custom-file-label" id="input-file"></span>
                                </label>
                            </div>
                            {!! $errors->first('file', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">File harus berformat .xls / .xslx</small>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Save</button>
                <a href="{{ asset('storage/FormatUploadAnggota.xlsx') }}" class="btn btn-success"><span class="fa fa-download"></span> Download Format</a>
            </div>
        </form>
    </div>
@endif
@endsection

@section('scripts')
    <script src="{{ asset('js/file-upload.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.preview').click(function(e){
			const name = $(this).data('name');
			const code = $(this).data('code');
			const region = $(this).data('region');

			$('#h5-detail-anggota-keluar').html(`Preview <strong>${name}</strong>`);
			$('.code').html(`: <strong>${code}</strong>`);
			$('.name').html(`: <strong>${name}</strong>`);
			$('.region').html(`: <strong>${region}</strong>`);

			$('#modal-preview-anggota-keluar').modal('show');
		})
        });
    </script>
@endsection