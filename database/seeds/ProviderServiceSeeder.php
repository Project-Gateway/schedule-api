<?php

use Illuminate\Database\Seeder;

class ProviderServiceSeeder extends Seeder
{

    use \Database\seeds\SeederTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fields = ['provider_id', 'service_id'];
        $data = [
            ['6adaf9d0-d34c-11e8-8aa8-dbec492e6487', 1],
            ['6adaf9d0-d34c-11e8-8aa8-dbec492e6487', 2],
        ];

        $this->seedData('provider_service', $fields, $data, ['provider_id', 'service_id'], false);
    }
}
