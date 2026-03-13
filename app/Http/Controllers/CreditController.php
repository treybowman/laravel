<?php

namespace App\Http\Controllers;

use App\Models\CreditPackage;
use App\Models\PromoCode;
use App\Models\PromoCodeRedemption;
use App\Services\CreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreditController extends Controller
{
    public function __construct(private CreditService $creditService)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $packages = CreditPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $transactions = $user->creditTransactions()
            ->orderByDesc('created_at')
            ->paginate(15);

        $stripeKey = setting('stripe.publishable_key', '');

        return view('credits.index', compact('packages', 'transactions', 'stripeKey'));
    }

    public function redeem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $user = $request->user();
        $promo = PromoCode::where('code', strtoupper($validated['code']))->first();

        if (!$promo || !$promo->isValid()) {
            return redirect()->back()->with('error', 'Invalid or expired promo code.');
        }

        $alreadyRedeemed = PromoCodeRedemption::where('promo_code_id', $promo->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyRedeemed) {
            return redirect()->back()->with('error', 'You have already redeemed this promo code.');
        }

        PromoCodeRedemption::create([
            'promo_code_id' => $promo->id,
            'user_id' => $user->id,
            'credits_amount' => $promo->credits_amount,
            'created_at' => now(),
        ]);

        $promo->increment('uses_count');

        $this->creditService->addCredits(
            $user,
            $promo->credits_amount,
            'promo_redemption',
            'Promo code: ' . $promo->code,
            PromoCode::class,
            $promo->id
        );

        return redirect()->back()->with('success', "Successfully redeemed {$promo->credits_amount} credits!");
    }

    public function purchase(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:credit_packages,id',
            'payment_intent_id' => 'required|string',
        ]);

        $user = $request->user();
        $package = CreditPackage::findOrFail($validated['package_id']);

        if (!$package->is_active) {
            return response()->json(['error' => 'This package is no longer available.'], 422);
        }

        // Verify the payment intent with Stripe
        $secretKey = setting('stripe.secret_key', '');

        if (empty($secretKey)) {
            return response()->json(['error' => 'Payment processing is not configured.'], 422);
        }

        try {
            \Stripe\Stripe::setApiKey($secretKey);
            $intent = \Stripe\PaymentIntent::retrieve($validated['payment_intent_id']);

            if ($intent->status !== 'succeeded') {
                return response()->json(['error' => 'Payment not completed.'], 422);
            }

            $this->creditService->addCredits(
                $user,
                $package->credits,
                'purchase',
                "Purchased {$package->name} package",
                CreditPackage::class,
                $package->id
            );

            // Update the transaction with the payment intent ID
            $user->creditTransactions()
                ->where('type', 'purchase')
                ->orderByDesc('created_at')
                ->first()
                ?->update(['stripe_payment_intent_id' => $validated['payment_intent_id']]);

            return response()->json([
                'success' => true,
                'credits' => $package->credits,
                'new_balance' => $user->fresh()->credits_balance,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment verification failed.'], 422);
        }
    }
}
