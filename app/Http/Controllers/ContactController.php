<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        if (config('services.turnstile.secret_key')) {
            $turnstileResponse = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret'   => config('services.turnstile.secret_key'),
                'response' => $request->input('cf-turnstile-response'),
                'remoteip' => $request->ip(),
            ]);

            if (! ($turnstileResponse->json('success') ?? false)) {
                throw ValidationException::withMessages([
                    'cf-turnstile-response' => 'Пожалуйста, подтвердите, что вы не робот.',
                ]);
            }
        }

        app(TelegramService::class)->notifyContactForm(
            $request->input('name'),
            $request->input('email'),
            $request->input('message'),
        );

        return back()->with('success', 'Сообщение отправлено. Мы свяжемся с вами в ближайшее время.');
    }
}
