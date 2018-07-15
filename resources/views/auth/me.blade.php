@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2" id="me_div">
		
			<div class="panel panel-default">
				@if($update_user)
					<div class="panel-heading">{{{$user->username}}}: &nbsp;&nbsp;<a href="/password/email">Reset Password</a></div>
					
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
					
					@if (Session::has('bad_message'))
						<div class="alert alert-danger">
							{{{ Session::get('bad_message') }}}
						</div>
					@endif
					
					<form class="form-horizontal" role="form" method="POST" action="/auth/update">
					
						<input type="hidden" name="_token" value="{{{ csrf_token() }}}">

						<div class="form-group">
							<label class="col-md-4 control-label">Email Address*</label>
							<div class="col-md-6">
								<input type="email" class="form-control" name="email" value="{{{ $user->email }}}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">First Name</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="firstname" value="{{{ $user->firstname }}}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Last Name</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="lastname" value="{{{ $user->lastname }}}">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Update
								</button>
							</div>
						</div>
						
					</form>
				@else
					<div class="panel-heading">{{{$user->username}}}: &nbsp;&nbsp;<a href="/auth/me/update_user">Update</a> &nbsp;&middot;&nbsp; <a href="/password/email">Reset Password</a></div>
						<div style="margin-top: 20px; margin-bottom: 20px;">
							@if (Session::has('good_message'))
								<div class="alert alert-success">
									{{{ Session::get('good_message') }}}
								</div>
							@endif
							<p style="padding: 2px 0px 2px 20px;">Email Address:&nbsp; {{{$user->email}}}</p>
							<p style="padding: 2px 0px 2px 20px;">First Name:&nbsp; {{{$user->firstname}}}</p>
							<p style="padding: 2px 0px 2px 20px;">Last Name:&nbsp; {{{$user->lastname}}}</p>
						</div>
				@endif
			</div>
			
			<br>
			
			<div class="panel panel-default">
				<div class="panel-heading">Usage Stats:</div>
					<div style="margin-top: 20px; margin-bottom: 20px;">
						<p style="padding: 2px 0px 2px 20px;">Posted Proofs:&nbsp; {{{ $total_proofs }}}</p>
						<p style="padding: 2px 0px 2px 20px;">Flagged Proofs Accurate:&nbsp; {{{ $total_flags }}}</p>
						<p style="padding: 2px 0px 2px 20px;">Posted Comments:&nbsp; {{{ $total_comments }}}</p>
					</div>
			</div>
			
			<br>
			
			@if($total_proofs > 0)
				<div>
					<label>Proofs Posted:</label>
					@foreach($proofs as $proof)
					<p>
						<a href="/mathproofs/{{{ $proof->slug_id }}}">{{{ $proof->theorem_words }}}</a><br>
						<span class="created_on">Posted on {{{ Carbon::parse($proof->created_at)->toFormattedDateString() }}}.</span>
					</p>
					@endforeach
				</div>
			@endif
				
			</div>
		</div>
	</div>
@endsection

