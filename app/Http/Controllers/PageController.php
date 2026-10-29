<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function about()
    {
        return view('public.pages.about');
    }

    public function contact()
    {
        return view('public.pages.contact');
    }

    public function employers()
    {
        return view('public.pages.employers');
    }

    public function terms()
    {
        return view('public.pages.terms');
    }

    public function privacy()
    {
        return view('public.pages.privacy');
    }

    public function pricing()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();

        return view('public.pages.pricing', compact('plans'));
    }

    public function contactStore(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email'], 'subject' => ['required', 'string', 'max:255'], 'message' => ['required', 'string', 'max:3000']]);
        Mail::raw("Contact form from {$data['name']} ({$data['email']}): {$data['subject']}\n\n{$data['message']}", function ($m) {
            $m->to(config('mail.from.address'))->subject('TalentFlow contact form');
        });

        return back()->with('status', 'Thank you — your message has been received. We will reply soon.');
    }

    public function newsletter(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'unique:newsletter_subscribers,email'], 'name' => ['nullable', 'string', 'max:255'], 'consent' => ['accepted']]);
        NewsletterSubscriber::create(['email' => $data['email'], 'name' => $data['name'] ?? null, 'consent_given' => true]);

        return back()->with('status', 'You are subscribed. Welcome to the TalentFlow newsletter.');
    }
}
