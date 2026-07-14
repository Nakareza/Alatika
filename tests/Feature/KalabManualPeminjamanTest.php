<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\PeminjamanController as AdminPeminjamanController;
use App\Http\Controllers\Kalab\PeminjamanController;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class KalabManualPeminjamanTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_loan_for_registered_user_is_linked_to_that_user_history(): void
    {
        $kalab = User::factory()->create(['role' => 'kalab']);
        $borrower = User::factory()->create(['role' => 'mahasiswa']);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = Alat::create([
            'nama' => 'Oscilloscope',
            'kode' => 'INV-OSC',
            'stok_total' => 5,
            'stok_tersedia' => 5,
            'status' => 'tersedia',
            'kategori' => 'Elektronik',
            'kategori_id' => $kategori->id,
        ]);

        $telegram = Mockery::mock(TelegramService::class);
        $telegram->shouldIgnoreMissing();
        $telegram->shouldReceive('notifyPeminjamanApproved')->andReturn(true);

        $this->actingAs($kalab);

        $controller = app(PeminjamanController::class);
        $request = new Request([
            'user_id' => $borrower->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
            'keperluan' => 'Praktikum',
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDay()->toDateString(),
            'approvers' => [],
        ]);

        $response = $controller->storeManual($request, $telegram);

        $this->assertNotNull($response->getTargetUrl());

        $peminjaman = Peminjaman::latest()->first();
        $this->assertNotNull($peminjaman);
        $this->assertSame($borrower->id, $peminjaman->user_id);
        $this->assertSame($borrower->name, $peminjaman->nama_peminjam);
        $this->assertSame('dipinjam', $peminjaman->status);
    }

    public function test_admin_can_approve_manual_loan_for_non_user_organization(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kalab = User::factory()->create(['role' => 'kalab']);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = Alat::create([
            'nama' => 'Multimeter',
            'kode' => 'INV-MULTI-2',
            'stok_total' => 3,
            'stok_tersedia' => 3,
            'status' => 'tersedia',
            'kategori' => 'Elektronik',
            'kategori_id' => $kategori->id,
        ]);

        $peminjaman = Peminjaman::create([
            'kode_peminjaman' => Peminjaman::generateKode(),
            'user_id' => null,
            'nama_peminjam_non_user' => 'UKM Robotika',
            'alat_id' => $alat->id,
            'jumlah' => 1,
            'keperluan' => 'Monitoring',
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDay()->toDateString(),
            'status' => 'pending',
            'required_approvals' => ['admin'],
            'kalab_approved_by' => $kalab->id,
            'kalab_approved_at' => now(),
        ]);

        $telegram = Mockery::mock(TelegramService::class);
        $telegram->shouldIgnoreMissing();
        $telegram->shouldReceive('notifyPeminjamanApproved')->andReturn(true);

        $this->actingAs($admin);

        $controller = app(AdminPeminjamanController::class);
        $response = $controller->approve($peminjaman->id, $telegram);

        $peminjaman->refresh();

        $this->assertSame('dipinjam', $peminjaman->status);
        $this->assertSame($admin->id, $peminjaman->admin_approved_by);
        $this->assertNotNull($response->getSession()->get('success'));
    }

    public function test_kalab_can_approve_manual_loan_for_non_user_organization(): void
    {
        $kalab = User::factory()->create(['role' => 'kalab']);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = Alat::create([
            'nama' => 'Multimeter',
            'kode' => 'INV-MULTI',
            'stok_total' => 3,
            'stok_tersedia' => 3,
            'status' => 'tersedia',
            'kategori' => 'Elektronik',
            'kategori_id' => $kategori->id,
        ]);

        $peminjaman = Peminjaman::create([
            'kode_peminjaman' => Peminjaman::generateKode(),
            'user_id' => null,
            'nama_peminjam_non_user' => 'UKM Robotika',
            'alat_id' => $alat->id,
            'jumlah' => 1,
            'keperluan' => 'Monitoring',
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDay()->toDateString(),
            'status' => 'pending',
            'required_approvals' => null,
        ]);

        $telegram = Mockery::mock(TelegramService::class);
        $telegram->shouldIgnoreMissing();
        $telegram->shouldReceive('notifyPeminjamanApproved')->andReturn(true);

        $this->actingAs($kalab);

        $controller = app(PeminjamanController::class);
        $response = $controller->approve(new Request(), $peminjaman->id, $telegram);

        $peminjaman->refresh();

        $this->assertSame('dipinjam', $peminjaman->status);
        $this->assertSame($kalab->id, $peminjaman->kalab_approved_by);
        $this->assertNotNull($response->getSession()->get('success'));
    }

    public function test_kalab_can_mark_manual_loan_as_completed_when_item_is_returned(): void
    {
        $kalab = User::factory()->create(['role' => 'kalab']);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = Alat::create([
            'nama' => 'Logic Analyzer',
            'kode' => 'INV-LA-01',
            'stok_total' => 2,
            'stok_tersedia' => 1,
            'status' => 'tersedia',
            'kategori' => 'Elektronik',
            'kategori_id' => $kategori->id,
        ]);

        $peminjaman = Peminjaman::create([
            'kode_peminjaman' => Peminjaman::generateKode(),
            'user_id' => null,
            'nama_peminjam_non_user' => 'UKM Robotika',
            'alat_id' => $alat->id,
            'jumlah' => 1,
            'keperluan' => 'Workshop',
            'tanggal_pinjam' => now()->subDay()->toDateString(),
            'tanggal_kembali' => now()->addDay()->toDateString(),
            'status' => 'dipinjam',
            'kalab_approved_by' => $kalab->id,
            'kalab_approved_at' => now(),
        ]);

        $this->actingAs($kalab);

        $controller = app(PeminjamanController::class);
        $response = $controller->completeReturn(new Request([
            'kondisi_kembali' => 'baik',
            'catatan_kondisi' => 'Barang dikembalikan lengkap',
        ]), $peminjaman->id);

        $peminjaman->refresh();
        $alat->refresh();

        $this->assertSame('selesai', $peminjaman->status);
        $this->assertSame('baik', $peminjaman->kondisi_kembali);
        $this->assertSame(2, $alat->stok_tersedia);
        $this->assertNotNull($response->getSession()->get('success'));
    }
}
