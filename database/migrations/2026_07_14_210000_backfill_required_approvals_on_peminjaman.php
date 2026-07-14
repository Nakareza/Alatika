<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Alat;

return new class extends Migration
{
    public function up(): void
    {
        Peminjaman::chunk(100, function ($peminjamans) {
            foreach ($peminjamans as $p) {
                if ($p->required_approvals !== null) {
                    continue;
                }

                $approvals = null;

                if ($p->user_id === null) {
                    // Manual external loan (UKM/Organisasi/Luar)
                    $approvals = ['admin', 'kalab'];
                } else {
                    $user = User::find($p->user_id);
                    if ($user) {
                        if ($user->role === 'dosen') {
                            $approvals = ['kalab', 'kaprodi'];
                        } elseif ($user->role === 'mahasiswa') {
                            // Check tool type
                            $isKhusus = false;
                            if ($p->alat_id) {
                                $alat = Alat::find($p->alat_id);
                                if ($alat && $alat->program_studi !== null) {
                                    $isKhusus = true;
                                }
                            } elseif ($p->borrowable_type === 'App\Models\Alat') {
                                $alat = Alat::find($p->borrowable_id);
                                if ($alat && $alat->program_studi !== null) {
                                    $isKhusus = true;
                                }
                            }
                            $approvals = $isKhusus ? ['kalab'] : ['admin'];
                        }
                    }
                }

                if ($approvals !== null) {
                    $p->required_approvals = $approvals;
                    $p->save();
                }
            }
        });
    }

    public function down(): void
    {
        // No down migration needed for data backfill
    }
};
