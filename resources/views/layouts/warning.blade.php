@if (Session::get('warning'))
	<div class="alert alert-dark-warning alert-dismissible fade show">
	    <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
	    <i class="fa fa-exclamation-triangle"></i>
	    <strong>Warning!</strong> 
	    {!! session('warning') !!}
	</div>
@endif

@if (Session::get('success'))
	<div class="alert alert-dark-success alert-dismissible fade show">
	    <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
	    <i class="fa fa-check"></i>
	    <strong>Success!</strong> 
	    {!! session('success') !!}
	</div>
@endif

@if (Session::get('info'))
	<div class="alert alert-dark-primary alert-dismissible fade show">
	    <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
	    <i class="fa fa-info"></i>
	    <strong>Info!</strong> 
	    {!! session('info') !!}
	</div>
@endif

@if (Session::get('danger'))
	<div class="alert alert-dark-danger alert-dismissible fade show">
	    <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
	    <i class="fa fa-ban"></i>
	    <strong>Danger!</strong> 
	    {!! session('danger') !!}
	</div>
@endif