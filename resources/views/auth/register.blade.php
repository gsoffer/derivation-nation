@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Register</div>
				<div class="panel-body">
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
					
					<form class="form-horizontal" role="form" method="POST" action="/auth/register">
						<input type="hidden" name="_token" value="{{{ csrf_token() }}}">

						<div class="form-group">
							<label class="col-md-4 control-label">Username*</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="username" value="{{{ old('username') }}}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">First Name</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="firstname" value="{{{ old('firstname') }}}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Last Name</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="lastname" value="{{{ old('lastname') }}}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Email Address*</label>
							<div class="col-md-6">
								<input type="email" class="form-control" name="email" value="{{{ old('email') }}}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Password*</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Confirm Password*</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password_confirmation">
							</div>
						</div><br>
						
						<div id="agreement">
							@include('auth.agreement')
						</div>
						<div id="acknowledge">By pressing "Register", I acknowledge that I accept these terms and conditions.</div>
						
						<br>
						
						<div class="form-group">
							<div style="margin-left: auto; margin-right: auto; width: 90px;">
								<button type="submit" class="btn btn-primary">
									Register
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

