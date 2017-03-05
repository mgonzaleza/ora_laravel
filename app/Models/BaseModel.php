<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

abstract class BaseModel extends Model {

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [];

	/**
	 * Storage for Validation errors.
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Validation rules
	 * @var array
	 */
	protected $rules = [];

	/**
	 * Method to validate data before further processing.
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function validate(array $data) {

		$validator = Validator::make($data, $this->rules);

		if ($validator->fails()) {
			$this->errors = $validator->errors()->messages();
			return false;
		}

		return true;
	}

	/**
	 * Get Validation errors
	 * @return array
	 */
	public function getValidationErrors() {
		return (array) $this->errors;
	}
}
