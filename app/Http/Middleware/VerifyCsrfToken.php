<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\Security\Core\Util\StringUtils;
use Request as FacadeRequest;
use Session;
use Redirect;
use Auth;

class VerifyCsrfToken implements Middleware {

	/**
	 * The encrypter implementation.
	 *
	 * @var \Illuminate\Contracts\Encryption\Encrypter
	 */
	protected $encrypter;

	/**
	 * Create a new middleware instance.
	 *
	 * @param  \Illuminate\Contracts\Encryption\Encrypter  $encrypter
	 * @return void
	 */
	public function __construct(Encrypter $encrypter)
	{
		$this->encrypter = $encrypter;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 *
	 * @throws \Illuminate\Session\TokenMismatchException
	 */
	public function handle($request, Closure $next)
	{
		if ($this->isReading($request) || $this->tokensMatch($request)) {
			return $this->addCookieToResponse($request, $next($request));
		}
		else {
			if(FacadeRequest::method() == 'POST') {
				if(in_array(FacadeRequest::path(), array('comments', 'accurateflags', 'mathproofs', 'auth/update'))){
					if(Auth::user()) {
						Auth::logout();
					}
					Session::flash('orig_post_path', FacadeRequest::path());
					Session::flash('orig_post_input', FacadeRequest::input());
					return Redirect::to('/auth/login')->with('bad_message', 'Sorry, your session may have expired, but logging in below will resubmit your post.');
				}
				else {
					return Redirect::back()->with('request', $request)->with('bad_message', 'Sorry, your session may have expired. Please try again.');
				}
			}
			else {
				return Redirect::back()->with('bad_message', 'Sorry, your session may have expired. Please try again.');
			}
		}
	}

	/**
	 * Determine if the session and input CSRF tokens match.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return bool
	 */
	protected function tokensMatch($request)
	{
		$token = $request->session()->token();

		$header = $request->header('X-XSRF-TOKEN');

		return StringUtils::equals($token, $request->input('_token')) ||
		       ($header && StringUtils::equals($token, $this->encrypter->decrypt($header)));
	}

	/**
	 * Add the CSRF token to the response cookies.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Illuminate\Http\Response  $response
	 * @return \Illuminate\Http\Response
	 */
	protected function addCookieToResponse($request, $response)
	{
		$response->headers->setCookie(
			new Cookie('XSRF-TOKEN', $request->session()->token(), time() + 60 * 120, '/', null, false, false)
		);

		return $response;
	}

	/**
	 * Determine if the HTTP request uses a ‘read’ verb.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return bool
	 */
	protected function isReading($request)
	{
		return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
	}

}
