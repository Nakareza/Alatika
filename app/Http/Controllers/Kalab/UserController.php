<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display filtered users (admin, mahasiswa, kaprodi)
     */
    public function index(Request $request)
    {
        $query = User::query()->whereIn('role', ['admin', 'mahasiswa', 'kaprodi']);

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search by name/email/nim/nip
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('kalab.users.index', compact('users'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('kalab.users.create');
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $rules = [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'role'          => 'required|in:admin,mahasiswa,kaprodi',
            'program_studi' => 'nullable|in:D3 IK,D4 TRK',
        ];

        if ($request->role === 'mahasiswa') {
            $rules['nim'] = 'required|string|max:20|unique:users,nim';
        } else {
            $rules['nip'] = 'nullable|string|max:30|unique:users,nip';
        }

        $request->validate($rules, [
            'nim.required' => 'NIM wajib diisi untuk mahasiswa.',
            'nim.unique'   => 'NIM sudah terdaftar.',
            'nip.unique'   => 'NIP sudah terdaftar.',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'nim'           => $request->nim,
            'nip'           => $request->nip,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'program_studi' => $request->program_studi,
        ]);

        return redirect()->route('kalab.users.index')
                         ->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Update user role and prodi
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role'          => 'required|in:admin,mahasiswa,kaprodi',
            'program_studi' => 'nullable|in:D3 IK,D4 TRK',
        ]);

        $user->update([
            'role'          => $request->role,
            'program_studi' => $request->program_studi,
        ]);

        return redirect()->route('kalab.users.index')
                         ->with('success', "Data {$user->name} berhasil diperbarui!");
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('kalab.users.index')
                         ->with('success', 'User berhasil dihapus!');
    }
}
