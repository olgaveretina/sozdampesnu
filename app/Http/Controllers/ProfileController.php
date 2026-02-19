<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->latest()->get();

        return view('profile.index', compact('orders'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        auth()->user()->update($data);

        return back()->with('success', 'Имя обновлено.');
    }

    public function updateEmail(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(auth()->id())],
        ]);

        auth()->user()->update($data);

        return back()->with('success', 'Email обновлён.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('success', 'Пароль изменён.');
    }

    public function generateTelegramToken(Request $request)
    {
        $token = Str::random(32);

        auth()->user()->update(['telegram_bind_token' => $token]);

        $botUsername = config('services.telegram.bot_username');

        return redirect("https://t.me/{$botUsername}?start={$token}");
    }

    public function unlinkTelegram(Request $request)
    {
        auth()->user()->update([
            'telegram_chat_id'    => null,
            'telegram_bind_token' => null,
        ]);

        return back()->with('success', 'Telegram отвязан.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = auth()->user();
        auth()->logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Аккаунт удалён.');
    }
}
