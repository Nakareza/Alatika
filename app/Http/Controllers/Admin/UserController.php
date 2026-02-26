<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display all users
     */
    public function index(Request $request)
    {
        $query = User::query();

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

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:admin,dosen,kalab,mahasiswa',
        ];

        // NIM required for mahasiswa, NIP for dosen/kalab/admin
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
            'name'     => $request->name,
            'email'    => $request->email,
            'nim'      => $request->nim,
            'nip'      => $request->nip,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,dosen,kalab,mahasiswa',
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->route('admin.users.index')
                         ->with('success', "Role {$user->name} berhasil diubah menjadi {$request->role}!");
    }

    /**
     * Delete a user
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', "User {$user->name} berhasil dihapus!");
    }
}
