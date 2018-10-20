<?php

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
        $this->call(WorkingTimesSeeder::class);
        $this->call(ServicesSeeder::class);
        $this->call(ProviderServiceSeeder::class);
    }
}
