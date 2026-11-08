<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Membuat akun admin awal.
 * PASSWORD WAJIB DIGANTI SEBELUM DEPLOY!
 *
 * Akun staf TIDAK dibuat lewat pendaftaran publik (rules A10).
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'admin@silago.id'],
            [
                'name'              => 'Admin SILAGO',
                'email_verified_at' => now(),
                'password'          => Hash::make('admin12345!'),
            ]
        )->forceFill(['role' => 'admin', 'status' => 'active'])->saveQuietly();

        $this->command->warn('⚠️  Ganti password akun admin sebelum deploy!');
        $this->command->info('Admin: admin@silago.id / admin12345!');
    }
}
