<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class ChatController extends BaseController {

    /**
     * Display a listing of the resource.
     *
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function index() {
    	$search_text = request()->input('s');

    	$page_limit = request()->input('limit', 5);
	    $page_limit = (1 > $page_limit) ? 10 : (int) $page_limit;

	    $chats = Chat::search($search_text);
	    $chats_paginated = $chats->paginate($page_limit);

	    // create custom pagination, because standard pagination toolset doesn't work as expected
	    // and doesn't provide all needed data
	    $page_count = ceil(count($chats->get()) / $page_limit);
		$pagination_data = [
			'page_count' => $page_count,
			'current_page' => $chats_paginated->currentPage(),
			'has_next_page' => $chats_paginated->currentPage() < $page_count,
			'has_prev_page' => $chats_paginated->currentPage() > 1,
			'count' => $chats_paginated->count(),
			'limit' => $page_limit,
		];

	    return $this->renderSuccessJsonChatData($chats_paginated->getCollection(), $pagination_data);
    }

    /**
     * Store a newly created resource in storage.
     *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse|mixed
	 */
    public function store(Request $request) {
	    $chat = new Chat();
	    if ($chat->validate($request->all())) {

		    $chat = Chat::create([
			    'name'    => $request->input('name'),
			    'user_id' => Auth::user()->id,
		    ]);

		    return $this->renderSuccessJsonChatData($chat);

	    } else {
		    return $this->renderFailedJson($chat->getValidationErrors());
	    }
    }

	/**
	 * Helper to render JSON with populated Chat data.
	 *
	 * @param $chat
	 * @param array|null $pagination_data
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function renderSuccessJsonChatData($chat, $pagination_data = null) {
		$output_data = [];
		if ($chat instanceof Chat) {
			$output_data = [
				'id' => $chat->id,
				'user_id' => $chat->user_id,
				'name' => $chat->name,
				'created' => $chat->created_at->format('Y-m-d\TH:i:s\Z'),
				'user' => [
					'id' => $chat->user->id,
					'name' => $chat->user->name,
				],
				'last_message' => null,
			];
		} elseif ($chat instanceof Collection) {
			foreach ($chat as $chat_item) {
				$recent_message_data = null;
				if ($chat_item->recentMessage()) {
					$recent_message_data = [
						'id'      => $chat_item->recentMessage()->id,
						'user_id' => $chat_item->recentMessage()->user_id,
						'chat_id' => $chat_item->recentMessage()->chat_id,
						'message' => $chat_item->recentMessage()->message,
						'created' => $chat_item->recentMessage()->created_at->format('Y-m-d\TH:i:s\Z'),
						'user' => [
							'id' => $chat_item->recentMessage()->user->id,
							'name' => $chat_item->recentMessage()->user->name,
						],
					];
				}

				$output_data[] = [
					'id' => $chat_item->id,
					'user_id' => $chat_item->user_id,
					'name' => $chat_item->name,
					'created' => $chat_item->created_at->format('Y-m-d\TH:i:s\Z'),
					'user' => [
						'id' => $chat_item->user->id,
						'name' => $chat_item->user->name,
					],
					'last_message' => $recent_message_data,
				];
			}
		}

		return $this->renderSuccessJson($output_data, $pagination_data);
	}
}
