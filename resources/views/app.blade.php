<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Derivation Nation</title>
	<link href="/css/app.css" rel="stylesheet">
</head>

<body>
	<div id="wrapper">
	
		<nav class="navbar navbar-default">
			<div class="container-fluid">
			
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#dn-navbar-collapse">
						<span class="sr-only">Toggle Navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/">Derivation Nation</a>
				</div>
	
				<div class="collapse navbar-collapse" id="dn-navbar-collapse">
					<ul class="nav navbar-nav">
						<li><a href="/">Home</a></li>
						<li><a href="/mathproofs">Math Proofs</a></li>
						<li><a href="/mathproofs/create">Post a Proof</a></li>
					</ul>

					<ul class="nav navbar-nav navbar-right">
						@if(Auth::guest())
							<li><a href="/auth/login">Login</a></li>
							<li><a href="/auth/register">Register</a></li>
						@else
							<li><a href="/auth/me">{{{ Auth::user()->username }}}</a></li>
							<li><a href="/auth/logout">Logout</a></li>
						@endif
					</ul>
				</div>
				
			</div>
		</nav>
		
		<br>
	
		<div id="content">
			@yield('content')
		</div>

		<div id="footer">
			<div>
				<ul>
					<li><a href="/contact_us">Contact Us</a></li>
					<li>© DerivationNation.com, 2015</li>
				</ul>
			</div>
		</div>

	</div>
	
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
	
</body>

</html>
