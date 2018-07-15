@extends('app')

@section('content')

@if (Session::has('good_message'))
	<div class="alert alert-success">
		{{{ Session::get('good_message') }}}
	</div>
@endif

@if (Session::has('bad_message'))
	<div class="alert alert-danger">
		{{{ Session::get('bad_message') }}}
	</div>
@endif

<br>
<img src="images/LogoLong.jpg" alt="Derivation Nation Logo" id="logolong" />
<img src="images/Logo.jpg" alt="Derivation Nation Logo" height="220px" id="logo" />
<br><br><br>

<form class="form-horizontal" role="form" method="POST" action="/mathproofs/search">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<div class="form-group">
		<div class="col-md-8 col-md-offset-2">
			<input type="text"  onfocus="this.value = '';" id="home_search_input" class="form-control" name="home_search" value="e.g. &quot;sum of angles in triangle equals 180 degrees&quot;, &quot;triangle sum theorem&quot;, or &quot;geometry&quot;">
		</div>
	</div>
	<div class="form-group">
		<div id="home_submit">
			<button type="submit" class="btn btn-primary">Search Proofs</button>
		</div>
	</div>
</form>
@endsection

