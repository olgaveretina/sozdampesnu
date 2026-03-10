<?php

namespace App\Http\Controllers;

use App\Models\GiftCertificate;
use App\Services\YooKassaService;
use Illuminate\Http\Request;

class GiftCertificateController extends Controller
{
    public function index()
    {
        return view('certificates.index');
    }

    public function show(\App\Models\GiftCertificate $cert)
    {
        abort_if($cert->buyer_user_id !== auth()->id(), 403);
        abort_unless($cert->code, 404);

        return view('certificates.show', compact('cert'));
    }

    public function store(Request $request)
    {
        // Resolve amount: preset or custom
        $amount = $request->input('amount');
        if ($amount === 'custom') {
            $amount = $request->input('custom_amount');
        }

        $request->merge(['amount' => (int) $amount]);
        $request->validate([
            'amount' => ['required', 'integer', 'min:100'],
        ]);

        $amount = (int) $request->input('amount');

        $cert = GiftCertificate::create([
            'amount_rub'    => $amount,
            'is_used'       => false,
            'buyer_user_id' => auth()->id(),
        ]);

        $payment = $cert->payment()->create([
            'amount' => $amount,
            'status' => 'pending',
        ]);

        try {
            $url = app(YooKassaService::class)->createCertificatePayment($payment, $cert);
            return redirect($url);
        } catch (\Exception $e) {
            $payment->delete();
            $cert->delete();
            return back()->with('error', 'Ошибка при создании платежа. Попробуйте позже.');
        }
    }
}
