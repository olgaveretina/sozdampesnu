<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;

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

        app(TelegramService::class)->notifyContactForm(
            $request->input('name'),
            $request->input('email'),
            $request->input('message'),
        );

        return back()->with('success', 'Сообщение отправлено. Мы свяжемся с вами в ближайшее время.');
    }
}
