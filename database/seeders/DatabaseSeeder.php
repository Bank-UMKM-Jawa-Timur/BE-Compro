<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // AdministratorSeeder::class,
            // ProfilSeeder::class,
            // KebijakanPrivasiSeeder::class,
            // SyaratDanKetentuanSeeder::class,
            // AboutSeeder::class,
        ]);
    }
}
