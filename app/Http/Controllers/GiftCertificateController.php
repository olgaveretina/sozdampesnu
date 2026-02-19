<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GiftCertificateController extends Controller
{
    public function index()
    {
        return view('certificates.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'integer', 'min:100'],
        ]);

        // TODO: Phase 5 — create payment and generate certificate

        return back()->with('error', 'Оплата будет доступна в ближайшее время.');
    }
}
