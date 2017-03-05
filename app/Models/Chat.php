<?php

namespace App\Models;

class Chat extends BaseModel {

	/**
	 * Get the index name for the model.
	 *
	 * @return string
	 */
	public function searchableAs()
	{
		return 'chats_index';
	}

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'user_id'];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = ['user_id'];

	/**
	 * Get User for the Chat.
	 */
	public function user() {
		return $this->belongsTo('App\Models\User');
	}

	/**
	 * Get Messages for the Chat.
	 */
	public function messages() {
		return $this->hasMany('App\Models\Message');
	}

	/**
	 * Get the Recent Messages for the Chat.
	 */
	public function recentMessage() {
		return $this->messages()->latest()->first();
	}

 	/**
	 * Validation rules
	 * @var array
	 */
	protected $rules = [
		'name' => 'required|min:1|max:255',
	];
}
