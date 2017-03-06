<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'auth'], function(Router $api) {
        //$api->post('signup', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');
        $api->post('login', 'App\\Http\\Controllers\\Api\\v1\\LoginController@login');
        $api->post('logout', 'App\\Http\\Controllers\\Api\\v1\\LogoutController@logout');
        //$api->post('recovery', 'App\\Api\\V1\\Controllers\\ForgotPasswordController@sendResetEmail');
        //$api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');
    });

    $api->group(['prefix' => 'users'], function(Router $api) {
        $api->post('', 'App\\Http\\Controllers\\Api\\v1\\UserController@create');
        $api->get('{user_id}', 'App\\Http\\Controllers\\Api\\v1\\UserController@show');
        $api->put('', 'App\\Http\\Controllers\\Api\\v1\\UserController@update');
    });

    $api->group(['prefix' => 'chats'], function(Router $api) {
        $api->get('', 'App\\Http\\Controllers\\Api\\v1\\ChatController@index');
        $api->post('', 'App\\Http\\Controllers\\Api\\v1\\ChatController@create');
        $api->put('', 'App\\Http\\Controllers\\Api\\v1\\ChatController@update');
    });

    $api->group(['prefix' => 'messages'], function(Router $api) {
        $api->get('{chat_id}', 'App\\Http\\Controllers\\Api\\v1\\MessagesController@index');
        $api->post('', 'App\\Http\\Controllers\\Api\\v1\\MessagesController@create');
    });

    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
        $api->get('protected', function() {
            return response()->json([
                'message' => 'Access to this item is only for authenticated user. Provide a token in your request!'
            ]);
        });

        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request.'
                ]);
            }
        ]);
    });

    $api->get('hello', function() {
        return response()->json([
            'message' => 'Sup!'
        ]);
    });
});
