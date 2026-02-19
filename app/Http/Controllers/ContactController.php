<?php

namespace App\Http\Controllers;

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

        // TODO: send email / Telegram notification in Phase 6

        return back()->with('success', 'Сообщение отправлено. Мы свяжемся с вами в ближайшее время.');
    }
}
