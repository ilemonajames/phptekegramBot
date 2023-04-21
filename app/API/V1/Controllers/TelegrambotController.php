<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Laravel\Facades\Telegram;
use Validator;

class SubscriptionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/subscribe",
     *     summary="Subscribe users to a chat bot",
     *     description="Subscribe users to a chat bot",
     *     tags={"Subscription"},
     *     @OA\Parameter(
     *         name="chat_id",
     *         in="query",
     *         description="Chat ID of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="bot_token",
     *         in="query",
     *         description="Telegram Bot API token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully subscribed user to chat bot"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function subscribeToChatBot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|integer',
            'bot_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation error',
                'message' => $validator->errors(),
            ], 400);
        }

        // Subscribe user to chat bot using Telegram SDK
        $response = Telegram::bot($request->bot_token)->sendMessage([
            'chat_id' => $request->chat_id,
            'text' => 'You have been subscribed to the chat bot',
        ]);

        return response()->json([
            'message' => 'Successfully subscribed user to chat bot',
            'response' => $response,
        ], 200);
    }

   
    public function subscribeToChannel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|integer',
            'channel_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation error',
                'message' => $validator->errors(),
            ], 400);
        }

        // Subscribe user to channel using Telegram SDK
        $response = Telegram::bot(config('telegram.bots.main_token'))->sendChatAction([
            'chat_id' => $request->chat_id,
            'action' => 'typing',
        ]);

        $response = Telegram::bot(config('telegram.bots.main_token'))->joinChat([
            'chat_id' => $request->channel_name,
        ]);

        return response()->json([
            'message' => 'Successfully subscribed user to channel',
            'response' => $response,
        ], 200);
    }

}
