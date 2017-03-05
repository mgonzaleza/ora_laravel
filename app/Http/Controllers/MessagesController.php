<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class MessageController extends BaseController {

	/**
	 * Display all Messages for the provided Chat id.
	 *
	 * @param $chat_id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index($chat_id)
	{
		$chat = Chat::find($chat_id);
		if( ! $chat) {
			return $this->renderFailedJson('Can\'t proceed with a wrong Chat id');
		}

		$page_limit = request()->input('limit', 5);
		$page_limit = (1 > $page_limit) ? 10 : (int) $page_limit;

		$messages = Message::where('chat_id', $chat->id)->paginate($page_limit);

		// create custom pagination, because standard pagination toolset doesn't work as expected
		// and doesn't provide all needed data
		$page_count = ceil($messages->total() / $messages->perPage());

		$pagination_data = [
			'page_count' => $page_count,
			'current_page' => $messages->currentPage(),
			'has_next_page' => $messages->currentPage() < $page_count,
			'has_prev_page' => $messages->currentPage() > 1,
			'count' => $messages->count(),
			'limit' => $page_limit,
		];

		return $this->renderSuccessJsonMessageData($messages->getCollection(), $pagination_data);
	}

	/**
	 * Store a newly created Message.
	 *
	 * @param $chat_id
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store($chat_id, Request $request) {
		$chat = Chat::find($chat_id);
		if( ! $chat) {
			return $this->renderFailedJson('Can\'t proceed with a wrong Chat id');
		}

		$message = new Message();
		if ($message->validate($request->all())) {

			$message = Message::create([
				'message' => $request->input('message'),
				'chat_id' => $chat->id,
				'user_id' => Auth::user()->id,
			]);

			return $this->renderSuccessJsonMessageData($message);

		} else {
			return $this->renderFailedJson($message->getValidationErrors());
		}
	}

	/**
	 * Helper to render JSON with populated Message data.
	 *
	 * @param $message
	 * @param array|null $pagination_data
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function renderSuccessJsonMessageData($message, $pagination_data = null) {
		$output_data = [];
		if ($message instanceof Message) {
			$output_data = [
				'id'      => $message->id,
				'chat_id' => $message->chat_id,
				'user_id' => $message->user_id,
				'message' => $message->message,
				'created' => $message->created_at->format('Y-m-d\TH:i:s\Z'),
				'user' => [
					'id'   => $message->user->id,
					'name' => $message->user->name,
				],
			];
		} elseif ($message instanceof Collection) {
			foreach ($message as $message_item) {
				$output_data[] = [
					'id'      => $message_item->id,
					'chat_id' => $message_item->chat_id,
					'user_id' => $message_item->user_id,
					'message' => $message_item->message,
					'created' => $message_item->created_at->format('Y-m-d\TH:i:s\Z'),
					'user' => [
						'id'   => $message_item->user->id,
						'name' => $message_item->user->name,
					],
				];
			}
		}

		return $this->renderSuccessJson($output_data, $pagination_data);
	}
}
