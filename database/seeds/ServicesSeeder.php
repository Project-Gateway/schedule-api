<?php

use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{

    use \Database\seeds\SeederTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fields = ['id', 'name', 'price'];
        $data = [
            [1, 'Boxing', 20],
            [2, 'Power Lifting', 20],
        ];

        $this->seedData('services', $fields, $data);
    }
}
