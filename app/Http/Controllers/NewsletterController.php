<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsletterSubscriptionRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    /**
     * Subscribe an email address to the newsletter.
     */
    public function subscribe(StoreNewsletterSubscriptionRequest $request): JsonResponse
    {
        $email = mb_strtolower(trim($request->validated('email')));

        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber && $subscriber->isSubscribed()) {
            return response()->json([
                'message' => 'You are already subscribed to the newsletter.',
            ], 409);
        }

        if ($subscriber) {
            $subscriber->forceFill([
                'name' => $subscriber->name ?: $request->validated('name'),
                'token' => $subscriber->token ?: (string) Str::uuid(),
                'subscribed' => true,
                'unsubscribed_at' => null,
            ])->save();

            return response()->json([
                'message' => 'Welcome back! You have been resubscribed.',
            ]);
        }

        NewsletterSubscriber::create([
            'email' => $email,
            'name' => $request->validated('name'),
            'token' => (string) Str::uuid(),
            'subscribed' => true,
        ]);

        return response()->json([
            'message' => 'Thanks for subscribing!',
        ]);
    }

    /**
     * Show the unsubscribe confirmation page.
     */
    public function unsubscribe(Request $request): View
    {
        $subscriber = NewsletterSubscriber::where('token', $request->route('token'))->firstOrFail();

        return view('newsletter.unsubscribe', [
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * Confirm the unsubscribe for the subscriber.
     */
    public function unsubscribeConfirm(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $subscriber = NewsletterSubscriber::where('token', $request->route('token'))->firstOrFail();

        $subscriber->forceFill([
            'subscribed' => false,
            'unsubscribed_at' => now(),
        ])->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'You have been unsubscribed from the newsletter.',
            ]);
        }

        return back()->with('status', 'You have been unsubscribed from the newsletter.');
    }
}
