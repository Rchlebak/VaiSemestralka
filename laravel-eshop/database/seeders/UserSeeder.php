<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder - vytvorí testovacích používateľov
 * Admin a zákazník pre testovanie
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin používateľ
        User::updateOrCreate(
            ['email' => 'admin@eshop.sk'],
            [
                'name' => 'Admin Administrátor',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
                'phone' => '+421 900 000 001',
            ]
        );

        // Testovací zákazník
        User::updateOrCreate(
            ['email' => 'zakaznik@example.sk'],
            [
                'name' => 'Test Zákazník',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_CUSTOMER,
                'phone' => '+421 900 000 002',
                'address' => 'Testovacia ulica 123',
                'city' => 'Bratislava',
                'zip' => '81101',
            ]
        );

        $this->command->info('Vytvorení používatelia:');
        $this->command->info('  Admin: admin@eshop.sk / admin123');
        $this->command->info('  Zákazník: zakaznik@example.sk / password123');
    }
}
