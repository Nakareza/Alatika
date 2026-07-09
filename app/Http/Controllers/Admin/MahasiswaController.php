<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = User::query()->where('role', 'mahasiswa');

        $query = clone $baseQuery;

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        if ($request->filled('telegram_status')) {
            if ($request->telegram_status === 'linked') {
                $query->whereNotNull('telegram_chat_id');
            } elseif ($request->telegram_status === 'unlinked') {
                $query->whereNull('telegram_chat_id');
            }
        }

        $mahasiswa = $query
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

        return view('admin.mahasiswa.index', compact('mahasiswa', 'stats'));
    }

    public function exportCsv(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data_mahasiswa_' . date('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $query = User::query()->where('role', 'mahasiswa');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        if ($request->filled('telegram_status')) {
            if ($request->telegram_status === 'linked') {
                $query->whereNotNull('telegram_chat_id');
            } elseif ($request->telegram_status === 'unlinked') {
                $query->whereNull('telegram_chat_id');
            }
        }

        $mahasiswa = $query->orderBy('name')->get();

        $callback = function () use ($mahasiswa) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, [
                'ID',
                'Nama',
                'NIM',
                'Email',
                'Status Telegram',
                'Chat ID Telegram',
                'Terdaftar Pada'
            ]);

            foreach ($mahasiswa as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->name,
                    $item->nim ?: '-',
                    $item->email,
                    $item->telegram_chat_id ? 'Tertaut' : 'Belum Tertaut',
                    $item->telegram_chat_id ?: '-',
                    $item->created_at?->format('Y-m-d H:i:s') ?: '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}