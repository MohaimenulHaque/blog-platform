<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactMessageMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Display the contact page.
     */
    public function show(): View
    {
        return view('pages.contact');
    }

    /**
     * Send the contact message to the configured recipient.
     */
    public function submit(StoreContactRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Mail::send(new ContactMessageMail(
            name: $validated['name'],
            email: $validated['email'],
            subject: $validated['subject'],
            message: $validated['message'],
        ));

        return back()->with('status', 'Thanks for reaching out! We will get back to you soon.');
    }
}
