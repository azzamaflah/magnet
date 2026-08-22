<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(
        protected ChatbotService $chatbotService
    ) {}

    /**
     * Handle chatbot message from user.
     */
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message'           => 'required|string|max:1000',
            'history'           => 'nullable|array|max:20',
            'history.*.role'    => 'required|in:user,model',
            'history.*.text'    => 'required|string|max:2000',
        ]);

        $message = $validated['message'];
        $history = $validated['history'] ?? [];

        $result = $this->chatbotService->generateReply($message, $history);

        return response()->json($result);
    }
}
