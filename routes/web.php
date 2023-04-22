<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Http\Controllers\Api\V1\ChatbotController;


$router->get('/', function () use ($router) {
    return $router->app->version();
});
// $router->get('/users', 'UserController@index');

// subscribe user to chatbot
Route::post('/v1/subscribe-chatbot', 'Api\V1\ChatbotController@subscribeToChatbot');

// subscribe user to channel
Route::post('/v1/subscribe-channel', 'Api\V1\ChatbotController@subscribeToChannel');

// send message to subscribers
Route::post('/v1/send-message', 'Api\V1\ChatbotController@sendMessage');

// webhook to receive responses from messenger API
Route::post('/v1/webhook', 'Api\V1\ChatbotController@webhook');