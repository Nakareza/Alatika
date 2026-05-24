<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = User::query()->where('role', 'dosen');

        $query = clone $baseQuery;

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($request->filled('telegram_status')) {
            if ($request->telegram_status === 'linked') {
                $query->whereNotNull('telegram_chat_id');
            } elseif ($request->telegram_status === 'unlinked') {
                $query->whereNull('telegram_chat_id');
            }
        }

        $dosen = $query
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => $baseQuery->count(),
            'linked_telegram' => (clone $baseQuery)->whereNotNull('telegram_chat_id')->count(),
            'unlinked_telegram' => (clone $baseQuery)->whereNull('telegram_chat_id')->count(),
            'registered_this_month' => (clone $baseQuery)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];

        return view('admin.dosen.index', compact('dosen', 'stats'));
    }
}