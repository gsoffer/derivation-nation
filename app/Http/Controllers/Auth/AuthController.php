<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Http\Request;
use App\Services\RegistrationConfirmation;
use App\Services\Mathproof;
use App\Services\Accurateflag;
use App\Services\Comment;
use App\User;
use Redirect;
use Mail;
use Session;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\AccurateflagsController;
use App\Http\Controllers\MathproofsController;
use App\Http\Requests\postRequest;
use Validator;

class AuthController extends Controller {

	protected $auth;
	protected $registrar;
	protected $redirectTo = '/';

	public function __construct(Guard $auth, Registrar $registrar) {
		
		$this->auth = $auth;
		$this->registrar = $registrar;
		
	}

	public function getRegister() {
		
		return view('auth.register');
		
	}

	public function postRegister(Request $request) {
		
		$validator = $this->registrar->validator($request->all());

		if ($validator->fails())
		{
			$this->throwValidationException(
				$request, $validator
			);
		}
		$new_user = $this->registrar->create($request->all());
		
		$confirmation_code = str_random(40);
		
		$new_registration_confirmation = new RegistrationConfirmation;
		$new_registration_confirmation->email = $new_user->email;
		$new_registration_confirmation->confirmation_code = $confirmation_code;
		$new_registration_confirmation->save();
		
		Mail::send('emails/verify_email', ['confirmation_code' => $confirmation_code], function($message) use($new_user) {
    		$message->to($new_user->email, $new_user->firstname . ' ' . $new_user->lastname)->subject("Thanks for signing up! Please verify your email address.");
		});
		
		return Redirect::to('/auth/login')->with('good_message', 'Thanks for registering! Please check your email and follow the link to verify your email address.');
		
	}
	
	public function getLogin() {
		
		if(Session::has('orig_post_path')) {
			Session::keep(['orig_post_path', 'orig_post_input']);
		}
		return view('auth.login');
		
	}
	
	public function postLogin(Request $request) {
		
		$this->validate($request, [
			'email' => 'required|exists:users,email',
			'password' => 'required'
		]);

		$credentials = $request->only('email', 'password');

		$not_verified = RegistrationConfirmation::where('email', '=', $request->email)->first();
		
		if(count($not_verified) > 0) {
			$user = User::where('email', '=', $request->email)->first();
			Mail::send('emails/verify_email', ['confirmation_code' => $not_verified->confirmation_code], function($message) use($user) {
    			$message->to($user->email, $user->firstname . ' ' . $user->lastname)->subject("Please verify your email address");
			});
			return Redirect::to('/auth/login')
						->with('bad_message', 'You must first verify your email address before being logged in. Please check your email and follow the link to verify your email address. This link has been resent for your convenience.');
		}
		else {}

		if ($this->auth->attempt($credentials, $request->has('remember'))) {
			if(Session::has('orig_post_path')) {
				$orig_post_path = Session::get('orig_post_path');
				$orig_post_input = Session::get('orig_post_input');
				$orig_request = new postRequest;
				$orig_request->replace($orig_post_input);
				if($orig_post_path == 'comments') {
					$controller = new CommentsController;
					return $controller->store($orig_request);
				}
				elseif($orig_post_path == 'accurateflags') {
					$controller = new AccurateflagsController;
					return $controller->store($orig_request);
				}
				elseif($orig_post_path == 'mathproofs') {
					$controller = new MathproofsController;
					return $controller->store($orig_request);
				}
				elseif($orig_post_path == 'auth/update') {
					return $this->updateUser($orig_request);
				}
				else {}
			}
			return redirect()->intended($this->redirectPath());
		}

		return redirect($this->loginPath())
					->withInput($request->only('email', 'remember'))
					->withErrors(['email' => 'These credentials do not match our records.']);
		
	}

	public function getLogout() {
		
		$this->auth->logout();
		return redirect('/');
		
	}

	public function redirectPath() {
		
		if (property_exists($this, 'redirectPath')) {
			return $this->redirectPath;
		}
		return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
		
	}

	public function loginPath() {
		
		return property_exists($this, 'loginPath') ? $this->loginPath : '/auth/login';
		
	}
	
	public function verifyEmail($confirmation_code = null) {
		
		if(isset($confirmation_code)) {
			$found = RegistrationConfirmation::where('confirmation_code', '=', $confirmation_code)->first();
			if(count($found) > 0) {
				$user = User::where('email', '=', $found->email)->first();
				RegistrationConfirmation::where('confirmation_code', '=', $confirmation_code)->delete();
				Mail::send('emails/welcome', ['firstname' => $user->firstname], function($message) use($user) {
    				$message->to($user->email, $user->firstname . ' ' . $user->lastname)->subject("Welcome!");
				});
				return Redirect::to('/auth/login')->with('good_message', 'Thanks for verifying your email address! Login below.');
			}
			else {
				return Redirect::to('/auth/login')->with('bad_message', 'Sorry, this confirmation link is invalid. Please check your email and make sure you copied it correctly into your browser.');
			}
		}
		else {
			return Redirect::to('/auth/login')->with('bad_message', 'Sorry, this confirmation link is invalid. Please check your email and make sure you copied it correctly into your browser.');
		}
		
	}
	
	public function me($update_user = null) {
		
		$user = $this->auth->user();
		$total_proofs = Mathproof::where('user_id', '=', $user->id)->count();
		$total_flags = Accurateflag::where('user_id', '=', $user->id)->count();
		$total_comments = Comment::where('user_id', '=', $user->id)->count();
		$proofs = Mathproof::where('user_id', '=', $user->id)->orderBy('created_at', 'desc')->get(['slug_id', 'theorem_words', 'created_at']);
		return view('auth.me', compact('user', 'update_user', 'total_proofs', 'total_flags', 'total_comments', 'proofs'));
		
	}
	
	public function update_me() {
		
		return $this->me(true);
		
	}
	
	protected function updateRules() {
		
		return [
			'firstname' => 'max:40|alpha_dash',
			'lastname' => 'max:40|alpha_dash',
			'email' => 'required|email|max:100'
		];
		
	}
	
	public function updateUser(postRequest $request) {
	
		$validator = Validator::make($request->all(), $this->updateRules());
		if ($validator->fails()) {
			return Redirect::to('/auth/me/update_user')->withErrors($validator->errors());
		}
		$user = $this->auth->user();
		$updating_email = 0;
		if($user->email != $request->email) {
			$found = User::where('email', '=', $request->email)->first();
			if(count($found) > 0) {
				return Redirect::to('/auth/me/update_user')->with('bad_message', 'This email address is already taken');
			}
			else {
				$updating_email = 1;
			}
		}
		$user->email = $request->email;
		$user->firstname = $request->firstname;
		$user->lastname = $request->lastname;
		$user->save();
		if($updating_email == 1) {
			Mail::send('emails/email_update', [], function($message) use($user) {
    			$message->to($user->email, $user->firstname . ' ' . $user->lastname)->subject("Email Address Updated");
			});
		}
		return Redirect::to('/auth/me')->with('good_message', 'Your updates have been made!');
		
	}

}
