<?php

use Illuminate\Database\Seeder;

class WorkingTimesSeeder extends Seeder
{

    use \Database\seeds\SeederTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fields = ['id', 'provider_id', 'date', 'start_time', 'finish_time'];
        $data = [
            [1, '6adaf9d0-d34c-11e8-8aa8-dbec492e6487', '2018-10-20', '08:00', '17:00'],
        ];

        $this->seedData('working_times', $fields, $data);
    }
}
