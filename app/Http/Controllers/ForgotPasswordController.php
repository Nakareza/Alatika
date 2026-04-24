<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
        ]);

        $user = User::where('nim', $request->nim)->orWhere('nip', $request->nim)->first();

        if (!$user) {
            return back()->withErrors(['nim' => 'NIM tidak ditemukan.']);
        }

        // Generate token
        $token = Str::random(64);

        // Simpan token ke database (gunakan table password_resets atau custom)
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        // Kirim email (untuk demo, kita return success)
        // Mail::to($user->email)->send(new ResetPasswordMail($token));

        return back()->with('status', 'Link reset kata sandi telah dikirim ke email Anda.');
    }
}