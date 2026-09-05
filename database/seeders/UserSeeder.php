<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membuat Akun ADMIN
        User::create([
            'name'              => 'Admin Subbagren',
            'nrp_nip'           => '123123',
            'jabatan'           => 'Admin Subbagren Bagrenmin SSDM Polri',
            'pangkat'           => 'KOMBES POL',
            'role'              => 'admin', // <-- Role Admin Diberikan di Sini
            'email'             => null,
            'email_verified_at' => now(),
            'password'          => Hash::make('subbagren123!'), // Password default
        ]);

        // 2. Membuat Akun Internal
        User::create([
            'name'              => 'Milzam DK',
            'nrp_nip'           => '199904082022021002',
            'jabatan'           => 'Banum Bagpangkat Robinkar SSDM Polri',
            'pangkat'           => 'PENDA',
            'role'              => 'internal', // <-- Role Admin Diberikan di Sini
            'email'             => null,
            'email_verified_at' => now(),
            'password'          => Hash::make('pangkat123!'), // Password default
        ]);
    }
}
