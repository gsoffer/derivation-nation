<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Redirect;

class PasswordController extends Controller {
	
	protected $redirectTo;
	protected $auth;
	protected $passwords;
	
	public function __construct(Guard $auth, PasswordBroker $passwords) {
		$this->auth = $auth;
		$this->passwords = $passwords;
	}
	
	public function getEmail() {
		
		return view('auth.password');
		
	}

	public function postEmail(Request $request) {
		
		$this->validate($request, ['email' => 'required']);

		$response = $this->passwords->sendResetLink($request->only('email'), function($m)
		{
			$m->subject($this->getEmailSubject());
		});

		switch ($response)
		{
			case PasswordBroker::RESET_LINK_SENT:
				return redirect()->back()->with('status', trans($response));

			case PasswordBroker::INVALID_USER:
				return redirect()->back()->withErrors(['email' => trans($response)]);
		}
		
	}

	protected function getEmailSubject() {
		
		return isset($this->subject) ? $this->subject : 'Your Password Reset Link';
		
	}

	public function getReset($token = null) {
		
		if (is_null($token))
		{
			throw new NotFoundHttpException;
		}

		return view('auth.reset')->with('token', $token);
		
	}

	public function postReset(Request $request) {
		
		$this->validate($request, [
			'token' => 'required',
			'email' => 'required',
			'password' => 'required|confirmed|min:6|max:60',
		]);
		
		$credentials = $request->only(
			'email', 'password', 'password_confirmation', 'token'
		);

		$response = $this->passwords->reset($credentials, function($user, $password) {
			$user->password = bcrypt($password);
			$user->save();
		});

		switch ($response) {
			case PasswordBroker::PASSWORD_RESET:
				if($this->auth->user()) {
					return Redirect::to('/')->with('good_message', 'Your password has been reset! Please use this password the next time you login.');
				}
				else{
					return Redirect::to('/auth/login')->with('good_message', 'Your password has been reset! Login below.');
				}
				
			default:
				return redirect()->back()
							->withInput($request->only('email'))
							->withErrors(['email' => trans($response)]);
		}
		
	}
	
	public function redirectPath() {
		
		if (property_exists($this, 'redirectPath')) {
			return $this->redirectPath;
		}
		return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
		
	}

}
