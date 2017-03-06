<?php
/**
 * @SWG\Swagger(
 *   @SWG\Info(
 *     title="ORA Chat API",
 *     version="1.0.0"
 *   )
 * )
 */
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;

class BaseController extends Controller {

	/**
	 * Statuses needed to render different JSON structures
	 * For more details see methods 'renderSuccessJson' and 'renderFailedJson'
	 */
	const STATUS_SUCCESS = 'success';
	const STATUS_FAILED  = 'failed';

	/**
	 * Helper to render JSON with the following structure:
	 *
	 * {
	 *   "success": true,
	 *   "data": {
	 *     "id": 1,
	 *     "token": "jYEGFnHmr2alLB1R1e8pEQpndpPPMqchVnx2ffI7K2feBItSIDH2lZ0c3kXmWzTicEG7vY1fMzh4FVUwqNNfKGJUfUxKV8iFsnEo7eL10jjYjIn3hCg",
	 *     "email": "user@test.com",
	 *     "name": "Name Lastname"
	 *   }
	 * }
	 *
	 * @param array $data
	 * @param array|null $pagination_data
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function renderSuccessJson(array $data, $pagination_data = null) {
		return $this->renderJson($data, self::STATUS_SUCCESS, $pagination_data);
	}

	/**
	 * Helper to render JSON with the following structure:
	 *
	 * {
	 *   "success": false,
	 *   "errors": {
	 *     "message": "The email has already been taken."
	 *   }
	 * }
	 *
	 * or
	 *
	 * {
	 *   "success": false,
	 *   "errors": {
	 *     "name": {
	 *       "The email has already been taken."
	 *     },
	 *     "email": {
	 *       "The email has already been taken."
	 *     },
	 *     "password": {
	 *       "The password has already been taken."
	 *     }
	 *   }
	 * }

	 *
	 * @param array|string $data
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function renderFailedJson($data) {
		if (is_string($data)) {
			$data = ['message' => $data];
		}

		return $this->renderJson((array) $data, self::STATUS_FAILED);
	}

	/**
	 * Helper to render output in JSON with needed structure.
	 * See methods 'renderSuccessJson' and 'renderFailedJson' for more details.
	 *
	 * @param array $data
	 * @param string $status
	 * @param array|null $pagination_data
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function renderJson(array $data, $status = self::STATUS_SUCCESS, $pagination_data = null) {
		$output_data = [];

		if(self::STATUS_SUCCESS == $status) {

			$output_data = [
				'success'    => true,
				'data'       => $data,
			];

			if ($pagination_data) {
				$output_data['pagination'] = $pagination_data;
			}
		} elseif (self::STATUS_FAILED == $status) {
			$output_data = [
				'success' => false,
				'errors'  => $data,
			];
		}

		return response()->json($output_data);
	}
}
