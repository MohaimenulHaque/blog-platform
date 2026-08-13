<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletter subscribers.
     */
    public function index(Request $request): View
    {
        $subscribers = NewsletterSubscriber::query()
            ->search($request->query('q'))
            ->when(
                $request->query('status') === 'subscribed',
                fn ($query) => $query->subscribed()
            )
            ->when(
                $request->query('status') === 'unsubscribed',
                fn ($query) => $query->where('subscribed', false)
            )
            ->latest()
            ->paginate(config('blog.pagination.admin_subscribers', 15))
            ->withQueryString();

        return view('admin.newsletter.index', [
            'subscribers' => $subscribers,
            'search' => trim((string) $request->query('q')),
            'filter' => $request->query('status', ''),
            'total' => NewsletterSubscriber::count(),
            'subscribedCount' => NewsletterSubscriber::subscribed()->count(),
        ]);
    }

    /**
     * Remove the specified subscriber.
     */
    public function destroy(Request $request, NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return back()->with('status', 'Subscriber removed.');
    }

    /**
     * Export the subscribers as a CSV download.
     */
    public function export(): StreamedResponse
    {
        return Response::streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Email', 'Name', 'Status', 'Subscribed At']);

            NewsletterSubscriber::query()
                ->orderBy('email')
                ->chunk(500, function ($subscribers) use ($handle): void {
                    foreach ($subscribers as $subscriber) {
                        fputcsv($handle, [
                            $subscriber->email,
                            $subscriber->name,
                            $subscriber->isSubscribed() ? 'subscribed' : 'unsubscribed',
                            $subscriber->created_at?->toDateTimeString(),
                        ]);
                    }
                });

            fclose($handle);
        }, 'newsletter-subscribers.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
