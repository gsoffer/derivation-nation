@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Contact Us</div>
				<div class="panel-body col-md-offset-1">

					@if (Session::has('good_message'))
						<div class="alert alert-success">
							{{{ Session::get('good_message') }}}
						</div>
					@endif
				
					@if (count($errors) > 0)
						<div class="alert alert-danger col-md-11 col-md-offset-0">
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

					<form class="form-horizontal" role="form" method="POST" action="/contact_us">
						<input type="hidden" name="_token" value="{{{ csrf_token() }}}">
						
						<div class="form-group">
							<div class="col-md-10 col-md-offset-0-p" style="padding: 0;">
								<span>We'd love to hear from you! Contact us with advice, questions, or concerns below:</span>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-offset-0-p">Name*</label>
							<div>
								<input type="text" class="form-control col-md-10 col-md-offset-0-p" name="name" value="{{{ Session::get('request')['name'] }}}">
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-md-offset-0-p">Email Address*</label>
							<div>
								<input type="email" class="form-control col-md-10 col-md-offset-0-p" name="email" value="{{{ Session::get('request')['email'] }}}">
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-offset-0-p">Subject*</label>
							<div>
								<input type="text" class="form-control col-md-10 col-md-offset-0-p" name="subject" value="{{{ Session::get('request')['subject'] }}}">
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-offset-0-p">Message*</label>
							<div>
								<textarea type="text" class="form-control col-md-10 col-md-offset-0-p" name="message">{{{ Session::get('request')['message'] }}}</textarea>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-md-offset-0-p" style="padding-top: 10px;">
								<button type="submit" class="btn btn-primary">
									Send Email
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
