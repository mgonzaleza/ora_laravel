<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat;
use App\Models\Message;

class MessagesController extends BaseController {

	/**
   * @SWG\Get(
   *   path="/api/messages/{chat_id}",
   *   summary="list of messages",
   *   tags={"Messages"},
	 *   @SWG\Parameter(
	 *     name="chat_id",
	 *     in="formData",
	 *     description="Chat ID",
	 *     required=true,
	 *     type="string"
	 *   ),
   *   @SWG\Response(
   *     response=200,
   *     description="A list of a chat messages"
   *   )
   * )
   */
	public function index($chat_id)
	{
		$chat = Chat::find($chat_id);
		if(!$chat) {
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
	 * @SWG\Post(
	 *   path="/api/messages",
	 *   summary="create new message for specific chat",
	 *   tags={"Messages"},
	 *   @SWG\Parameter(
	 *     name="chat_id",
	 *     in="formData",
	 *     description="Chat ID",
	 *     required=true,
	 *     type="string"
	 *   ),
	 *   @SWG\Parameter(
	 *     name="user_id",
	 *     in="formData",
	 *     description="User ID",
	 *     required=true,
	 *     type="string"
	 *   ),
	 *   @SWG\Parameter(
	 *     name="message",
	 *     in="formData",
	 *     description="Message",
	 *     required=true,
	 *     type="string"
	 *   ),
	 *   @SWG\Response(
	 *     response=400,
	 *     description="Invalid Authenticated Chat ID/User ID supplied"
	 *   ),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="Success"
	 *   ),
	 * )
	 */
	public function create(Request $request) {
		$chat = Chat::find($request->input('chat_id'));
		if(!$chat) {
			return $this->renderFailedJson('Can\'t proceed with a wrong Chat id');
		}

		$message = new Message();
		if ($message->validate($request->all())) {

			$message = Message::create([
				'message' => $request->input('message'),
				'chat_id' => $request->input('chat_id'),
				'user_id' => $request->input('user_id'),
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
