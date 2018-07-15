<?php namespace App\Http\Controllers;

use App\Http\Requests\postRequest;
use Validator;
use Redirect;
use Mail;

class HomeController extends Controller {

	public function index() {
		
		return view('home');
		
	}
	
	public function contactUs() {
		
		return view('contact_us');
		
	}
	
	protected function storeRules() {
		
		return [
			'name' => 'required',
			'email' => 'required',
			'subject' => 'required',
			'message' => 'required'
		];
		
	}
	
	public function emailUs(postRequest $request) {
	
		//First validate the request
		$validator = Validator::make($request->all(), $this->storeRules());
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator->errors())->with('request', $request->all());
		}
		
		Mail::send('emails/email_us', compact('request'), function($message) use($request) {
    		$message->to('gsoffer2@gmail.com', 'Derivation Nation')->subject(htmlspecialchars($request->subject));
		});
		
		return Redirect::to('/contact_us')->with('good_message', 'Thanks for your email! Your message has been sent!');
	
	}

}
