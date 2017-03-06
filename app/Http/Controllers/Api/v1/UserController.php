<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;

class UserController extends BaseController {

  /**
   * @SWG\Post(
   *   path="/api/users",
   *   summary="create user",
   *   tags={"Users"},
   *   @SWG\Parameter(
   *     name="name",
   *     in="formData",
   *     description="User Name",
   *     required=true,
   *     type="string"
   *   ),
   *   @SWG\Parameter(
   *     name="password",
   *     in="formData",
   *     description="User Password",
   *     required=true,
   *     type="string"
   *   ),
   *   @SWG\Parameter(
   *     name="email",
   *     in="formData",
   *     description="User Email",
   *     required=true,
   *     type="string"
   *   ),
   *   @SWG\Response(
   *     response=400,
   *     description="Invalid Authenticated UserId supplied"
   *   ),
   *   @SWG\Response(
   *     response=200,
   *     description="Success"
   *   ),
   * )
   */
    public function create(Request $request) {
	    $user = new User();
	    if (!$user->validate($request->all())) {
		    return $this->renderFailedJson($user->getValidationErrors());
	    }

	    $user = User::create([
		    'name'      => $request->input('name'),
		    'email'     => $request->input('email'),
		    'password'  => Hash::make($request->input('password'))
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
		    return $this->renderFailedJson('Can\'t find a user with that email and password.');
	    }
    }

    /**
     * @SWG\Get(
     *   path="/api/users/{user_id}",
     *   summary="show user data",
     *   tags={"Users"},
  	 *   @SWG\Parameter(
  	 *     name="user_id",
  	 *     in="formData",
  	 *     description="User ID",
  	 *     required=true,
  	 *     type="string"
  	 *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="User Data"
     *   )
     * )
     */
    public function show($user_id) {
      $user = User::find($user_id);
	    return $this->renderSuccessJsonUserData($user);
    }

    /**
     * @SWG\Put(
     *   path="/api/users",
     *   summary="update user",
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="formData",
     *     description="User ID",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     description="User Name",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     description="User Password",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="User Email",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=400,
     *     description="Invalid Authenticated UserId supplied"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Success"
     *   ),
     * )
     */
    public function update(Request $request) {

	    $user = User::find($request->input('user_id'));
	    if (!$user->validate($request->all(), User::VALIDATION_RULE_UPDATE)) {
		    return $this->renderFailedJson($user->getValidationErrors());
	    }

	    $user->name = $request->input('name');

	    // Oops! That email has already been taken
	    if($request->input('email') && ($user->email != $request->input('email'))) {
	        if(User::where('email',  $request->input('email'))->first()) {
			    return $this->renderFailedJson('That email has already been taken.');
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
