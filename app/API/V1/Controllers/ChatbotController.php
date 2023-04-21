<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Chatbot API",
 *      description="API endpoints for managing chatbot subscriptions and sending messages",
 *      @OA\Contact(
 *          email="contact@chatbotapi.com"
 *      )
 * )
 */

class ChatbotController extends Controller
{
    /**
     * @OA\Post(
     *      path="/subscribe",
     *      tags={"Chatbot"},
     *      summary="Subscribe a user to a chatbot",
     *      description="Subscribe a user to a chatbot by their chat ID",
     *      @OA\RequestBody(
     *          required=true,
     *          description="User's chat ID",
     *          @OA\JsonContent(
     *              required={"chat_id"},
     *              @OA\Property(property="chat_id", type="string", example="123456789")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User subscribed successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      )
     * )
     */
    public function subscribe(Request $request)
    {
        $chat_id = $request->input('chat_id');

        if (!$chat_id) {
            return response()->json(['error' => 'Chat ID is required'], 400);
        }

        // Save the chat ID to the database
        DB::table('subscriptions')->insert(['chat_id' => $chat_id]);

        return response()->json(['message' => 'User subscribed successfully'], 200);
    }

    /**
     * @OA\Post(
     *      path="/subscribe-channel",
     *      tags={"Chatbot"},
     *      summary="Subscribe a user to a channel or chat",
     *      description="Subscribe a user to a channel or chat by their chat ID",
     *      @OA\RequestBody(
     *          required=true,
     *          description="User's chat ID",
     *          @OA\JsonContent(
     *              required={"chat_id", "channel_id"},
     *              @OA\Property(property="chat_id", type="string", example="123456789"),
     *              @OA\Property(property="channel_id", type="string", example="@mychannel")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User subscribed successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      )
     * )
     */
    public function subscribeToChannel(Request $request)
{
    $chat_id = $request->input('chat_id');
    $channel_id = $request->input('channel_id');

    if (!$chat_id || !$channel_id) {
        return response()->json(['error' => 'Chat ID and channel ID are required'], 400);
    }

    // Subscribe the user to the channel using Telegram SDK
    $response = Telegram::bot()->post('https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendMessage', [
        'chat_id' => $chat_id,
        'text' => 'Please click the following link to join ' . $channel_id,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    ['text' => 'Join Channel', 'url' => 'https://t.me/' . substr($channel_id, 1)]
                ]
            ]
        ])
    ]);

    if ($response->ok) {
        return response()->json(['message' => 'User subscribed successfully'], 200);
    } else {
        return response()->json(['error' => 'Failed to subscribe user'], 500);
    }
}
}