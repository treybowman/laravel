<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\FeedbackRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function show(Request $request, string $token): View
    {
        $feedbackRequest = FeedbackRequest::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $user = $request->user();
        $isSeller = $feedbackRequest->seller_id === $user->id;
        $isBuyer = $feedbackRequest->buyer_id === $user->id;

        if (!$isSeller && !$isBuyer) {
            abort(403);
        }

        $alreadyGiven = $isSeller
            ? $feedbackRequest->seller_feedback_given
            : $feedbackRequest->buyer_feedback_given;

        $feedbackRequest->load(['listing.event', 'seller', 'buyer']);

        return view('feedback.show', compact('feedbackRequest', 'isSeller', 'isBuyer', 'alreadyGiven'));
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $feedbackRequest = FeedbackRequest::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $user = $request->user();
        $isSeller = $feedbackRequest->seller_id === $user->id;
        $isBuyer = $feedbackRequest->buyer_id === $user->id;

        if (!$isSeller && !$isBuyer) {
            abort(403);
        }

        $alreadyGiven = $isSeller
            ? $feedbackRequest->seller_feedback_given
            : $feedbackRequest->buyer_feedback_given;

        if ($alreadyGiven) {
            return redirect()->back()->with('error', 'You have already submitted feedback for this transaction.');
        }

        $validated = $request->validate([
            'rating' => 'required|in:positive,neutral,negative',
            'comment' => 'nullable|string|max:1000',
        ]);

        $revieweeId = $isSeller ? $feedbackRequest->buyer_id : $feedbackRequest->seller_id;
        $transactionType = $isSeller ? 'sell' : 'buy';

        Feedback::create([
            'reviewer_id' => $user->id,
            'reviewee_id' => $revieweeId,
            'listing_id' => $feedbackRequest->listing_id,
            'transaction_type' => $transactionType,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_visible' => true,
            'feedback_window_closes_at' => $feedbackRequest->expires_at,
        ]);

        if ($isSeller) {
            $feedbackRequest->update(['seller_feedback_given' => true]);
        } else {
            $feedbackRequest->update(['buyer_feedback_given' => true]);
        }

        return redirect()->back()->with('success', 'Feedback submitted successfully. Thank you!');
    }
}
