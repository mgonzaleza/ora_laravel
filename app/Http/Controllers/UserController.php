<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController {

    /**
     * Store a new User.
     *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function store(Request $request) {
	    $user = new User();
	    if ( ! $user->validate($request->all())) {
		    return $this->renderFailedJson($user->getValidationErrors());
	    }

	    $user = User::create([
		    'name'      => $request->input('name'),
		    'email'     => $request->input('email'),
		    'password'  => Hash::make($request->input('password')),
		    'api_token' => str_random(115),
	    ]);

	    return $this->renderSuccessJsonUserData($user);
    }

	/**
	 * Login
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function login(Request $request) {

	    $user = new User();
	    if ( ! $user->validate($request->all(), User::VALIDATION_RULE_LOGIN)) {
		    return $this->renderFailedJson($user->getValidationErrors());
	    }

	    $user = User::where('email', $request->input('email'))->first();
	    if($user && Hash::check($request->input('password'), $user->password)) {
		    return $this->renderSuccessJsonUserData($user);
	    } else {
		    return $this->renderFailedJson('Can\'t find a user with such email and password.');
	    }
    }

    /**
     * Display User data for the current User.
     *
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function show() {
	    return $this->renderSuccessJsonUserData(Auth::user());
    }

    /**
     * Update User.
     *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function update(Request $request) {

	    $user = Auth::user();
	    if ( ! $user->validate($request->all(), User::VALIDATION_RULE_UPDATE)) {
		    return $this->renderFailedJson($user->getValidationErrors());
	    }

	    $user->name = $request->input('name');

	    // don't let Users to use someone else's email
	    if($request->input('email') && ($user->email != $request->input('email'))) {
	        if(User::where('email',  $request->input('email'))->first()) {
			    return $this->renderFailedJson('The email has already been taken.');
		    } else {
		        $user->email = $request->input('email');
		    }
	    }

	    // update password if not empty
	    if( ! empty($request->input('password'))) {
		    $user->password = Hash::make($request->input('password'));
	    }

	    $user->save();
	    return $this->renderSuccessJsonUserData($user);
    }


	/**
	 * Helper to render JSON with populated User data.
	 *
	 * @param User $user
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function renderSuccessJsonUserData(User $user) {
		return $this->renderSuccessJson([
			'id'    => $user->id,
			'token' => $user->api_token,
			'email' => $user->email,
			'name'  => $user->name,
		]);
	}
}
