<?php namespace App\Http\Controllers;

use App\Services\Accurateflag;
use App\Services\Mathproof;
use App\Http\Requests\postRequest;
use Redirect;
use DB;
use Validator;
use Auth;

class AccurateflagsController extends Controller {

	protected function storeRules() {
		
		return [
			'slug_id' => 'required'
		];
		
	}
	
	protected function storeMessages() {
		
	    return [
	        'slug_id.required' => 'Sorry, your request could not be completed. Please try again.'
	    ];
	    
	}
	
	public function store(postRequest $request) {
		
		//First validate the request
		$validator = Validator::make($request->all(), $this->storeRules(), $this->storeMessages());
		if ($validator->fails()) {
			return Redirect::to('/mathproofs/' . $request->slug_id)->withErrors($validator->errors());
		}
		
		//Get the mathproof being flagged accurate
		$mathproof = Mathproof::where('slug_id', '=', $request->slug_id)->first(['id']);
		if(count($mathproof) == 0) {
			return Redirect::to('/mathproofs/' . $request->slug_id)->with('bad_message', 'Sorry, your request could not be completed. Please try again.');
		}
		else {
		 $mathproof_id = $mathproof->id;
		}
		
		//Check that the user did not already flag as accurate
		$existing_flag = Accurateflag::where('mathproof_id', '=', $mathproof_id)->where('user_id', '=', Auth::user()->id)->first();
		if(count($existing_flag) > 0) {
			return Redirect::to('/mathproofs/' . $request->slug_id)->with('bad_message', 'Sorry, you have already flagged this proof as accurate.');
		}
		
		//Save the new accurate flag
		$newaccurateflag_data = array(
			"mathproof_id" => $mathproof_id,
			"user_id" => Auth::user()->id
		);
		$new_accurateflag = Accurateflag::create($newaccurateflag_data);
		
		//Increment the total in the mathproofs table
		DB::table('mathproofs')->where('id', '=', $mathproof_id)->increment('flagged_accurate', 1);
		
		return Redirect::to('/mathproofs/' . $request->slug_id)->with('good_message', 'Thank you for confirming this proof is accurate!');
		
	}

}
