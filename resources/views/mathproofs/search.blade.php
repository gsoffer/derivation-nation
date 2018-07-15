@extends('app')

@section('content')
<script type="text/javascript" src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML"></script>

<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			
			<div class="panel panel-default">
			
				<div class="panel-heading">
					@if($recent == 1)
						Recently Posted
					@else
						Search Results
					@endif
				</div>

				<div class="panel-body results">
				
					@if (Session::has('bad_message'))
						<div class="alert alert-danger">
							{{{ Session::get('bad_message') }}}
						</div>
					@endif
					
					<form class="form-horizontal" role="form" method="POST" action="/mathproofs/search">
						<input type="hidden" name="_token" value="{{{ csrf_token() }}}">
						<div id="re_search_div">
							<div id="re_search_input_div">
								<input type="text" id="re_search_input" class="form-control active_input" name="re_search" value="{{{ $search }}}">
							</div>
							<div id="re_search_button_div">
								<button type="submit" id="re_search_submit" class="btn btn-primary">Search</button>
							</div>
						</div>
					</form>
					<br>
					
					@if(count($mathproofs) == 0)
						<div id="no_results">Sorry, no results matched your search criteria. Please try again!</div>
					@endif
					
					@foreach ($mathproofs as $mathproof)
						<br>
						<label>Theorem:&nbsp;&nbsp;</label><a href="{{action('MathproofsController@show', [$mathproof->slug_id])}}">{{{ $mathproof->theorem_words }}}</a>
						@if($mathproof->flagged_accurate > 0)
							&nbsp;&nbsp;&nbsp;{{{ $mathproof->flagged_accurate }}} x <span class="alpha" title="Flagged Accurate">\(\large \alpha\)</span>
						@endif
						<br>
						@if($mathproof->theorem_symbolic != '')
							<label>Symbolic:&nbsp;&nbsp;</label><span>{!! $mathproof->theorem_symbolic !!}</span>
						@endif
						<label>Branches:&nbsp;&nbsp;</label><span>{{{ $mathproof->branches }}}</span>
						<div class="created_on">
							Created by <span>{{{ $mathproof->username }}}</span>
							on <span>{{{ Carbon::parse($mathproof->created_at)->toFormattedDateString() }}}.</span>
						</div><br>
					@endforeach
					
				</div>
				
			</div>
		</div>
	</div>
</div>

<div class="results pagination">
	@if($previous_page == 1)
		<a href="{{{ URL::to($page_base_url . ($page - 1)) }}}"><strong>Previous</strong></a>&nbsp;&nbsp;
	@endif
	@for ($i = 1; $i <= $total_pages; $i++)
    	@if($i == $page)
    		<strong>{{{ $i }}}</strong>&nbsp;&nbsp;
    	@else
    		<a href="{{{ URL::to($page_base_url . $i) }}}">{{{ $i }}}</a>&nbsp;&nbsp;
    	@endif
	@endfor
	@if($next_page == 1)
		<a href="{{{ URL::to($page_base_url . ($page + 1)) }}}"><strong>Next</strong></a>
	@endif
</div>
@endsection
