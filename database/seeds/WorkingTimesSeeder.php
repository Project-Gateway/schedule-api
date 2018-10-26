<?php

use Carbon\Carbon;
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
        $date = Carbon::today()->subWeeks(4)->startOfWeek();
        $limit = $date->copy()->addWeeks(9)->endOfWeek();
        $fields = ['id', 'provider_id', 'date', 'start_time', 'finish_time'];
        $data = [];
        $i = 0;
        while ($date->lessThan($limit)) {
            if ($date->isWeekday()) {
                $start = $date->isTuesday() || $date->isThursday() ? '12:00' : '08:00';
                $end = $date->isTuesday() || $date->isThursday() ? '18:00' : '17:00';
                $data[] = [$i, '6adaf9d0-d34c-11e8-8aa8-dbec492e6487', $date->format('Y-m-d'), $start, $end];
                $i++;
            }
            $date->addDay();
        }

        $this->seedData('working_times', $fields, $data);
    }
}
