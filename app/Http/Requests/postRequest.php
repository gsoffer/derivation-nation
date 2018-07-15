<?php namespace App\Http\Requests;

use App\Http\Requests\Request;
use Redirect;

class postRequest extends Request {

	public function authorize() {
		
		return true;
		
	}
	
	public function rules() {
		
		return [];
		
	}

	public function messages() {
		
	    return [];
	    
	}

}
