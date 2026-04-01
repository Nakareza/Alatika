<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $peminjaman = \App\Models\Peminjaman::where('kode_peminjaman', 'PMJ-QNA4')->first();
    echo "Peminjaman: " . ($peminjaman ? 'Found' : 'Not Found') . "\n";
    if ($peminjaman) {
        echo "Status: " . $peminjaman->status . "\n";
        echo "User Role: " . $peminjaman->user->role . "\n";
        echo "Alat: " . $peminjaman->alat->nama . "\n";
        echo "Deadline: " . $peminjaman->tanggal_kembali->format('d M Y') . "\n";
        
        $adminUserId = \App\Models\User::where('role', 'admin')->first()->id;
        
        // Simulating the update
        /*
        $peminjaman->update([
            'status' => 'disetujui',
            'approved_by' => $adminUserId,
            'approved_at' => now(),
        ]);
        */
        echo "Simulated update successfully\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
} catch (\Error $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
