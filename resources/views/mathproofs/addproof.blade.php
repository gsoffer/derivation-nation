@extends('app')

@section('content')
<script src="../ckeditor/ckeditor.js"></script>

<script>
    CKEDITOR.on('instanceCreated', function (event) {
        var editor = event.editor,
            element = editor.element;
        if (element.getAttribute('id') == 'theorem_symbolic') {
            editor.on('configLoaded', function () {
					editor.config.toolbar = [['Maximize', 'Preview', 'Cut', 'Copy', 'Paste', 'Undo', 'Redo', 'Mathjax', 'Symbol']];
            });
        }
    });
</script>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-11-p col-md-offset-0-p">
			<div class="panel panel-default">
				<div class="panel-heading">Post Proof</div>
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

					<form id="add_proof_form" class="form-horizontal" role="form" method="POST" action="/mathproofs">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<label>Theorem in Words:</label>
						<textarea type="text" class="form-control active_input" name="theorem_words" id="theorem_words">{{{ Session::get('request')['theorem_words'] }}}</textarea><br><br>
						
						<label>Theorem in Math Symbols: (optional)</label>
						<textarea class="form-control" name="theorem_symbolic" id="theorem_symbolic" rows="1" cols="1">{{{ Session::get('request')['theorem_symbolic'] }}}</textarea><br><br>
						<script>CKEDITOR.replace('theorem_symbolic');</script>

						<label>Branches of Mathematics:</label>
						<textarea type="text" class="form-control active_input" name="branches" id="branches">{{{ Session::get('request')['branches'] }}}</textarea><br><br>

						<label>Proof:</label>
						<textarea class="form-control" name="proof" id="proof" rows="1" cols="1">{{{ Session::get('request')['proof'] }}}</textarea><br><br>
						<script>CKEDITOR.replace('proof');</script>

						<div id="proof_submit">
							<button type="submit" class="btn btn-primary">
								Add Proof
							</button>
						</div><br>
					</form>
					
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
