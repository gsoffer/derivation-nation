@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Reset Password</div>
				<div class="panel-body">
					@if (session('status'))
						<div class="alert alert-success">
							{{ session('status') }}
						</div>
					@endif

					@if (count($errors) > 0)
						<div class="alert alert-danger">
							Please resolve the below and try again!<br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{{ $error }}}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form class="form-horizontal" role="form" method="POST" action="/password/email">
						<input type="hidden" name="_token" value="{{{ csrf_token() }}}">

						<div class="form-group">
							<label class="col-md-4 control-label">Email Address</label>
							<div class="col-md-6">
								@if(Auth::user())
									<input type="email" class="form-control" name="email" value="{{{ Auth::user()->email }}}">
								@else
									<input type="email" class="form-control" name="email" value="{{{ old('email') }}}">
								@endif
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Send Password Reset Link
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
