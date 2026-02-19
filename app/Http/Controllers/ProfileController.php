<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
