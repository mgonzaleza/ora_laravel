<?php

namespace App\Models;

class Message extends BaseModel {

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['message', 'chat_id', 'user_id'];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = ['chat_id', 'user_id'];

	/**
	 * Get User for the Chat.
	 */
	public function user() {
		return $this->belongsTo('App\Models\User');
	}

	/**
	 * Get User for the Chat.
	 */
	public function chat() {
		return $this->belongsTo('App\Models\Chat');
	}

	/**
	 * Validation rules
	 * @var array
	 */
	protected $rules = [
		'message' => 'required|min:1|max:255',
	];

}
