<?php

namespace App\Http\Controllers;

use App\Jobs\CheckSavedSearchAlerts;
use App\Jobs\SocialPushJob;
use App\Models\Event;
use App\Models\FeedbackRequest;
use App\Models\Listing;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Listing::with(['event', 'venue', 'user'])->where('status', 'active');

        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('min_price')) {
            $query->where('asking_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('asking_price', '<=', $request->max_price);
        }

        if ($request->filled('quantity')) {
            $query->where('quantity', '>=', $request->quantity);
        }

        if ($request->filled('transfer_method')) {
            $query->where('transfer_method', $request->transfer_method);
        }

        if ($request->boolean('trade_only')) {
            $query->where('willing_to_trade', true);
        }

        if ($request->filled('section')) {
            $query->where('section', 'LIKE', '%' . $request->section . '%');
        }

        $query->orderByDesc('is_featured')->orderByDesc('created_at');

        $listings = $query->paginate(20)->withQueryString();
        $venues = Venue::where('is_active', true)->orderBy('sort_order')->get();

        return view('listings.index', compact('listings', 'venues'));
    }

    public function show(Listing $listing): View
    {
        if ($listing->status !== 'active') {
            abort(404);
        }

        $listing->increment('views_count');
        $listing->load(['event.venue', 'venue', 'user']);

        $existingConversation = null;
        if (auth()->check()) {
            $existingConversation = $listing->conversations()
                ->where('buyer_id', auth()->id())
                ->first();
        }

        return view('listings.show', compact('listing', 'existingConversation'));
    }

    public function create(Request $request): View
    {
        $step = (int) $request->session()->get('listing_step', 1);
        $data = $request->session()->get('listing_data', []);

        $venues = Venue::where('is_active', true)->orderBy('sort_order')->get();

        return match ($step) {
            2 => view('listings.create.step-2', compact('data', 'venues')),
            3 => view('listings.create.step-3', compact('data', 'venues')),
            4 => view('listings.create.step-4', compact('data', 'venues')),
            5 => view('listings.create.step-5', compact('data', 'venues')),
            default => view('listings.create.step-1', compact('data', 'venues')),
        };
    }

    public function store(Request $request): RedirectResponse
    {
        $step = (int) $request->session()->get('listing_step', 1);

        switch ($step) {
            case 1:
                $validated = $request->validate([
                    'venue_id' => 'required|exists:venues,id',
                    'event_id' => 'required|exists:events,id',
                ]);
                $request->session()->put('listing_data', array_merge(
                    $request->session()->get('listing_data', []),
                    $validated
                ));
                $request->session()->put('listing_step', 2);
                return redirect()->route('listings.create');

            case 2:
                $validated = $request->validate([
                    'title' => 'required|string|max:255',
                    'quantity' => 'required|integer|min:1|max:20',
                    'section' => 'nullable|string|max:50',
                    'row' => 'nullable|string|max:20',
                    'seat_numbers' => 'nullable|string|max:100',
                ]);
                $request->session()->put('listing_data', array_merge(
                    $request->session()->get('listing_data', []),
                    $validated
                ));
                $request->session()->put('listing_step', 3);
                return redirect()->route('listings.create');

            case 3:
                $validated = $request->validate([
                    'asking_price' => 'required|numeric|min:0.01',
                    'willing_to_trade' => 'nullable|boolean',
                    'trade_notes' => 'nullable|string|max:500',
                ]);
                $validated['willing_to_trade'] = $request->boolean('willing_to_trade');
                $request->session()->put('listing_data', array_merge(
                    $request->session()->get('listing_data', []),
                    $validated
                ));
                $request->session()->put('listing_step', 4);
                return redirect()->route('listings.create');

            case 4:
                $validated = $request->validate([
                    'transfer_method' => 'required|in:pdf_download,email_forward,mobile_transfer,will_call,in_person',
                    'payment_methods' => 'required|array|min:1',
                    'payment_methods.*' => 'in:venmo,cashapp,zelle,paypal,cash,other',
                    'notes' => 'nullable|string|max:1000',
                    'social_push' => 'nullable|boolean',
                    'affiliate_url' => 'nullable|url|max:500',
                    'affiliate_label' => 'nullable|string|max:100',
                ]);
                $validated['social_push'] = $request->boolean('social_push');
                $request->session()->put('listing_data', array_merge(
                    $request->session()->get('listing_data', []),
                    $validated
                ));
                $request->session()->put('listing_step', 5);
                return redirect()->route('listings.create');

            case 5:
                $data = $request->session()->get('listing_data', []);

                if (empty($data['venue_id']) || empty($data['event_id'])) {
                    $request->session()->forget(['listing_step', 'listing_data']);
                    return redirect()->route('listings.create')
                        ->with('error', 'Session expired. Please start over.');
                }

                $user = $request->user();
                $expiryDays = (int) setting('platform.listing_expiry_days', 30);
                $needsApproval = $user->isOnProbation();

                $listing = Listing::create([
                    'user_id' => $user->id,
                    'event_id' => $data['event_id'],
                    'venue_id' => $data['venue_id'],
                    'title' => $data['title'],
                    'quantity' => $data['quantity'],
                    'section' => $data['section'] ?? null,
                    'row' => $data['row'] ?? null,
                    'seat_numbers' => $data['seat_numbers'] ?? null,
                    'asking_price' => $data['asking_price'],
                    'willing_to_trade' => $data['willing_to_trade'] ?? false,
                    'trade_notes' => $data['trade_notes'] ?? null,
                    'transfer_method' => $data['transfer_method'],
                    'payment_methods' => $data['payment_methods'],
                    'notes' => $data['notes'] ?? null,
                    'status' => $needsApproval ? 'pending_approval' : 'active',
                    'social_push' => $data['social_push'] ?? false,
                    'affiliate_url' => $data['affiliate_url'] ?? null,
                    'affiliate_label' => $data['affiliate_label'] ?? null,
                    'expires_at' => Carbon::now()->addDays($expiryDays),
                ]);

                $request->session()->forget(['listing_step', 'listing_data']);

                if ($listing->status === 'active') {
                    dispatch(new CheckSavedSearchAlerts($listing->id));

                    if ($listing->social_push) {
                        dispatch(new SocialPushJob($listing->id));
                    }
                }

                $message = $needsApproval
                    ? 'Your listing has been submitted for review.'
                    : 'Your listing is now live!';

                return redirect()->route('listings.show', $listing)
                    ->with('success', $message);

            default:
                $request->session()->forget(['listing_step', 'listing_data']);
                return redirect()->route('listings.create');
        }
    }

    public function edit(Listing $listing): View
    {
        $this->authorize('update', $listing);
        $venues = Venue::where('is_active', true)->orderBy('sort_order')->get();
        $events = Event::where('venue_id', $listing->venue_id)->where('is_active', true)->get();
        return view('listings.edit', compact('listing', 'venues', 'events'));
    }

    public function update(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1|max:20',
            'section' => 'nullable|string|max:50',
            'row' => 'nullable|string|max:20',
            'seat_numbers' => 'nullable|string|max:100',
            'asking_price' => 'required|numeric|min:0.01',
            'willing_to_trade' => 'nullable|boolean',
            'trade_notes' => 'nullable|string|max:500',
            'transfer_method' => 'required|in:pdf_download,email_forward,mobile_transfer,will_call,in_person',
            'payment_methods' => 'required|array|min:1',
            'payment_methods.*' => 'in:venmo,cashapp,zelle,paypal,cash,other',
            'notes' => 'nullable|string|max:1000',
            'affiliate_url' => 'nullable|url|max:500',
            'affiliate_label' => 'nullable|string|max:100',
        ]);

        $validated['willing_to_trade'] = $request->boolean('willing_to_trade');

        $listing->update($validated);

        return redirect()->route('dashboard.listings')
            ->with('success', 'Listing updated successfully.');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        $this->authorize('delete', $listing);
        $listing->delete();
        return redirect()->route('dashboard.listings')
            ->with('success', 'Listing deleted.');
    }

    public function markSold(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        $validated = $request->validate([
            'buyer_username' => 'nullable|string|exists:users,username',
        ]);

        $listing->update(['status' => 'sold']);

        if (!empty($validated['buyer_username'])) {
            $buyer = User::where('username', $validated['buyer_username'])->first();
            if ($buyer) {
                $feedbackWindowDays = (int) setting('platform.feedback_window_days', 30);
                FeedbackRequest::create([
                    'listing_id' => $listing->id,
                    'seller_id' => $listing->user_id,
                    'buyer_id' => $buyer->id,
                    'token' => Str::random(64),
                    'expires_at' => now()->addDays($feedbackWindowDays),
                    'created_at' => now(),
                ]);
            }
        }

        return redirect()->route('dashboard.listings')
            ->with('success', 'Listing marked as sold.');
    }

    public function relist(Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        if (!in_array($listing->status, ['sold', 'expired'])) {
            return redirect()->route('dashboard.listings')
                ->with('error', 'Only sold or expired listings can be relisted.');
        }

        $user = auth()->user();
        if (!$user->canCreateListing()) {
            return redirect()->route('dashboard.listings')
                ->with('error', 'You have reached your active listing limit.');
        }

        $expiryDays = (int) setting('platform.listing_expiry_days', 30);
        $listing->update([
            'status' => $user->isOnProbation() ? 'pending_approval' : 'active',
            'expires_at' => now()->addDays($expiryDays),
            'views_count' => 0,
        ]);

        return redirect()->route('dashboard.listings')
            ->with('success', 'Listing relisted successfully.');
    }
}
