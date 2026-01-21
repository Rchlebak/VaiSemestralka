<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // OCHRANA: Ak už existujú produkty v databáze, neurobí sa nič
        if (Product::count() > 0) {
            $this->command->error('⚠️  ZASTAVENÉ! V databáze už existujú produkty.');
            $this->command->info('   Ak chcete naozaj resetovať databázu, najprv manuálne vymažte všetky produkty.');
            return;
        }

        // Spustenie seederov (len ak je databáza prázdna)
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
