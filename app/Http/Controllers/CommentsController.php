<?php namespace App\Http\Controllers;

use App\Services\Comment;
use App\Services\Mathproof;
use App\Helpers\Helper;
use DOMDocument;
use Redirect;
use App\Http\Requests\postRequest;
use Validator;
use Auth;

class CommentsController extends Controller {

	protected function storeRules() {
		
		return [
			'comment' => 'required',
			'slug_id' => 'required'
		];
		
	}

	protected function storeMessages() {
		
	    return [
	        'comment.required' => 'Your comment is blank.',
	        'slug_id.required' => 'Sorry, your comment could not be saved. Please try again.'
	    ];
	    
	}

	public function store(postRequest $request) {

		//First validate the request
		$validator = Validator::make($request->all(), $this->storeRules(), $this->storeMessages());
		if ($validator->fails()) {
			return Redirect::to('/mathproofs/' . $request->slug_id)->withErrors($validator->errors());
		}
		
		//Get the mathproof being commented on
		$mathproof = Mathproof::where('slug_id', '=', $request->slug_id)->first(['id']);
		if(count($mathproof) == 0) {
			return Redirect::to('/mathproofs/' . $request->slug_id)->with('bad_message', 'Sorry, your comment could not be saved. Please try again.');
		}
		else {
		 $mathproof_id = $mathproof->id;
		}
		
		//Sanitize comment
		$helper = new Helper;
		$comment_safe = $request->comment;
		$comment_safe = strip_tags($comment_safe, '<p><span>');
		$dom_comment = new DOMDocument();
		$dom_comment->loadHTML($comment_safe);
		$comment_safe = $helper->clean_html_by_tag($dom_comment, $comment_safe, 'p');
		$comment_safe = $helper->clean_html_by_tag($dom_comment, $comment_safe, 'span');
		$comment_safe = str_ireplace('<p>&nbsp;</p>', '', $comment_safe);
		$comment_safe = str_ireplace('<p></p>', '', $comment_safe);
		$comment_safe = str_ireplace('</p>', '<br>', $comment_safe);
		$comment_safe = str_ireplace('<p>', '', $comment_safe);
		$comment_safe = trim($comment_safe);
		if(trim(str_ireplace('<br>', '', str_ireplace('&nbsp;', '', $comment_safe))) == ''){
			return Redirect::to('/mathproofs/' . $request->slug_id)->with('bad_message', 'Your comment is blank.');
		}
		
		//Save the new comment
		$newcomment_data = array(
			"mathproof_id" => $mathproof_id,
			"user_id" => Auth::user()->id,
			"username" => Auth::user()->username,
			"comment" => $comment_safe
		);
		$new_comment = Comment::create($newcomment_data);
		return Redirect::to('/mathproofs/' . $request->slug_id)->with('good_message', 'Thanks for posting your comment!');
		
	}

}
