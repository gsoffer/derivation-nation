@extends('app')

@section('content')
<script type="text/javascript" src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML"></script>

<link rel="stylesheet" href="../ckeditor/plugins/codesnippet/lib/highlight/styles/default.css">
<script src="../ckeditor/plugins/codesnippet/lib/highlight/highlight.pack.js"></script>
<script>hljs.initHighlightingOnLoad();</script>

<script src="../ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.on('instanceCreated', function (event) {
        var editor = event.editor,
            element = editor.element;
        if (element.getAttribute('id') == 'comment') {
            editor.on('configLoaded', function () {
					editor.config.toolbar = [['Maximize', 'Preview', 'Cut', 'Copy', 'Paste', 'Undo', 'Redo', 'Mathjax', 'Symbol']];
            });
        }
    });
</script>

<div class="container">
	<div class="row">
		<div class="col-md-11-p col-md-offset-0-p">
			<div>

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

				@if (Session::has('good_message'))
					<div class="alert alert-success">
						{{{ Session::get('good_message') }}}
					</div>
				@endif

				<div id="showproof">
					
					<div id="showproof_by">Posted by {{{ $mathproof->username }}} on {{{ Carbon::parse($mathproof->created_at)->toFormattedDateString() }}}</div>
					<label>Theorem:&nbsp;&nbsp;</label><span>{{{ $mathproof->theorem_words }}}</span><br>
					@if($mathproof->theorem_symbolic != '')
						<label>Symbolic:&nbsp;&nbsp;</label><span>{!! $mathproof->theorem_symbolic !!}</span>
					@endif
					<label>Branches:&nbsp;&nbsp;</label><span>{{{ $mathproof->branches }}}</span><br>
					
					<br><br>
					
					@if($already_flagged == 1)
						{{{ $mathproof->flagged_accurate }}} x <span class="alpha" title="Flag Accurate">\(\large \alpha\)</span>ccurate
					@else
						<form class="form-horizontal" role="form" method="POST" action="/accurateflags">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="slug_id" value="{{{ $mathproof->slug_id }}}">
							<label>Proof of Theorem:&nbsp;&nbsp;&nbsp;</label>
							@if($mathproof->flagged_accurate > 0)
								{{{ $mathproof->flagged_accurate }}} x 
							@else
								Mark
							@endif
							<button type="submit" class="btn" id="flag_as_accurate">
								<span class="alpha" title="Flag Accurate">\(\large \alpha\)</span>ccurate
							</button>
						</form>
					@endif
					
					<div>{!! $mathproof->proof !!}</div>
					
					<br><br>
					
					@if(count($comments) > 0)
						<label>Comments:</label><br><br>
						@foreach ($comments as $comment)
							<div>
								<span class="comment_name">{{{ $comment->username }}} ({{{ Carbon::parse($comment->created_at)->toFormattedDateString() }}}):</span>
								<div class="comment_body">{!! $comment->comment !!}</div><br><br><br>
							</div>
						@endforeach
					@endif
					
					<label>Comment:</label>
					<form class="form-horizontal" role="form" method="POST" action="/comments">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="slug_id" value="{{{ $mathproof->slug_id }}}">
						<div class="form-group">
							<div class="col-md-12">
								<textarea class="form-control" name="comment" id="comment"></textarea>
								<script>CKEDITOR.replace('comment');</script>
							</div>
						</div>
						<div class="form-group">
							<div id="comment_submit">
								<button type="submit" class="btn btn-primary">
									Post Comment
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
