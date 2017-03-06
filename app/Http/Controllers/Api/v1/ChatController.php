<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class ChatController extends BaseController {

  /**
   * @SWG\Get(
   *   path="/api/chats",
   *   summary="list chats",
   *   tags={"Chats"},
   *   @SWG\Response(
   *     response=200,
   *     description="A list of chats"
   *   )
   * )
   */
    public function index() {
    	$search_text = request()->input('s');

    	$page_limit = request()->input('limit', 5);
	    $page_limit = (1 > $page_limit) ? 10 : (int) $page_limit;

	    $chats = Chat::all();
	    return $this->renderSuccessJsonChatData($chats);
    }

    /**
     * @SWG\Post(
     *   path="/api/chats",
     *   summary="list chats",
     *   tags={"Chats"},
     *   @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     description="Name of the Chat",
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
      $chat = new Chat();
	    if ($chat->validate($request->all())) {
        $valid_user = User::find($request->input('user_id'));
        if($valid_user) {
          $chat = Chat::create([
  			    'name'    => $request->input('name'),
  			    'user_id' => $request->input('user_id')
  		    ]);

  		    return $this->renderSuccessJsonChatData($chat);
        } else {
          $returnData = array(
            'message' => 'Not a valid user',
            'errors' => 'error',

          );
          return Response::json($returnData, 400);
        }
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
