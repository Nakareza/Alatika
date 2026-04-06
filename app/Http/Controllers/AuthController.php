<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'role' => 'required|in:mahasiswa,dosen',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];

        if ($request->role === 'dosen') {
            $rules['nomor_induk'] = 'required|string|max:30|unique:users,nip';
        } else {
            $rules['nomor_induk'] = 'required|string|max:20|unique:users,nim';
        }

        $validator = Validator::make($request->all(), $rules, [
            'nomor_induk.required' => 'Nomor Identitas wajib diisi.',
            'nomor_induk.unique' => 'Nomor Identitas sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'nim' => $request->role === 'mahasiswa' ? $request->nomor_induk : null,
            'nip' => $request->role === 'dosen' ? $request->nomor_induk : null,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        Auth::login($user);

        // Redirect based on role
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect based on role
            return $this->redirectBasedOnRole(Auth::user());
        }

        return redirect()->back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->withInput();
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome Admin!');
        }
        
        if ($user->role === 'kalab') {
            return redirect()->route('kalab.dashboard')->with('success', 'Welcome Kepala Laboratorium!');
        }

        if ($user->role === 'dosen') {
            return redirect()->route('dosen.dashboard')->with('success', 'Welcome Dosen!');
        }

        if ($user->role === 'mahasiswa') {
            return redirect()->route('mahasiswa.dashboard')->with('success', 'Welcome Mahasiswa!');
        }

        // Default redirect
        return redirect()->route('home')->with('success', 'Login successful!');
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login-new')->with('success', 'Logged out successfully!');
    }
}
