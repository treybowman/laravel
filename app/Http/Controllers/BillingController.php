<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $memberPriceId = setting('stripe.member_price_id', '');
        $proPriceId = setting('stripe.pro_price_id', '');
        $stripeKey = setting('stripe.publishable_key', '');

        $subscription = null;
        if ($user->subscribed()) {
            $subscription = $user->subscription();
        }

        return view('billing.index', compact('user', 'memberPriceId', 'proPriceId', 'stripeKey', 'subscription'));
    }
}
