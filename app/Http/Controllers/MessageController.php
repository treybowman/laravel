<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $conversations = Conversation::with(['listing.event', 'buyer', 'seller', 'messages' => function ($q) {
            $q->orderByDesc('created_at')->limit(1);
        }])
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
            })
            ->orderByDesc('updated_at')
            ->get();

        return view('messages.index', compact('conversations'));
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $user = $request->user();

        if ($conversation->buyer_id !== $user->id && $conversation->seller_id !== $user->id) {
            abort(403);
        }

        $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();

        // Mark messages as read
        $isbuyer = $conversation->buyer_id === $user->id;

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($isbuyer) {
            $conversation->update(['buyer_unread' => 0]);
        } else {
            $conversation->update(['seller_unread' => 0]);
        }

        return view('messages.show', compact('conversation', 'messages'));
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $user = $request->user();

        if ($conversation->buyer_id !== $user->id && $conversation->seller_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $validated['body'],
            'created_at' => now(),
        ]);

        // Increment unread count for the other party
        if ($conversation->buyer_id === $user->id) {
            $conversation->increment('seller_unread');
        } else {
            $conversation->increment('buyer_unread');
        }

        $conversation->touch();

        return redirect()->route('messages.show', $conversation);
    }

    public function startConversation(Request $request, Listing $listing): RedirectResponse
    {
        $user = $request->user();

        if ($listing->user_id === $user->id) {
            return redirect()->route('listings.show', $listing)
                ->with('error', 'You cannot message yourself.');
        }

        $conversation = Conversation::firstOrCreate(
            ['listing_id' => $listing->id, 'buyer_id' => $user->id],
            [
                'seller_id' => $listing->user_id,
                'status' => 'active',
            ]
        );

        return redirect()->route('messages.show', $conversation);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['count' => 0]);
        }

        $count = Conversation::where('buyer_id', $user->id)
            ->where('buyer_unread', '>', 0)
            ->sum('buyer_unread')
            + Conversation::where('seller_id', $user->id)
            ->where('seller_unread', '>', 0)
            ->sum('seller_unread');

        return response()->json(['count' => (int) $count]);
    }
}
