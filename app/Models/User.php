<?php

namespace App\Models;

use Illuminate\Support\Facades\Validator;
use Illuminate\Notifications\Notifiable;

class User extends BaseUserModel {

	/**
	 * Types used for Validation rules
	 */
	const VALIDATION_RULE_REGISTRATION = 'rules_registration';
	const VALIDATION_RULE_LOGIN        = 'rules_login';
	const VALIDATION_RULE_UPDATE       = 'rules_update';

	/**
	 * Validation rules
	 * @var array
	 */
	protected $rules = [
		self::VALIDATION_RULE_REGISTRATION => [
			'name'     => 'required|max:255',
			'email'    => 'required|email|max:255|unique:users',
			'password' => 'required|min:6|max:255',
			//'confirm'  => 'required|min:6|max:255|same:password',
		],
		self::VALIDATION_RULE_LOGIN        => [
			'email'    => 'required|email|max:255',
			'password' => 'required|min:6|max:255',
		],
		self::VALIDATION_RULE_UPDATE        => [
			'name'     => 'max:255',
			'email'    => 'email|max:255',
			'password' => 'min:6|max:255',
			//'confirm'  => 'required_with:password|min:6|max:255|same:password',
		],
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password', 'api_token',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token', 'api_token',
	];

	/**
	 * Get Chats for the User.
	 */
	public function chats() {
		return $this->hasMany('App\Models\Chat');
	}

	/**
	 * Method to validate data before further processing.
	 * Supports a vary of Validation rules (see constants above);
	 *
	 * @param array $data
	 * @param string $validation_mode
	 *
	 * @return bool
	 */
	public function validate(array $data, $validation_mode = self::VALIDATION_RULE_REGISTRATION) {

		if(!array_key_exists($validation_mode, $this->rules)) {
			$this->errors = ['validation' => "Can't proceed without existing validation rule."];
			return false;
		}

		$validator = Validator::make($data, $this->rules[$validation_mode]);

		if ($validator->fails()) {
			$this->errors = $validator->errors()->messages();
			return false;
		}

		return true;
	}
}
