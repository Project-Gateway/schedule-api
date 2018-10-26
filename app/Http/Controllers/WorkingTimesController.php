<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWorkingTime;
use App\Http\Requests\UpdateWorkingTime;
use App\Models\WorkingTime;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkingTimesController extends Controller
{

    /**
     * @param $year
     * @param $week
     * @return array
     */
    public function byWeek(Request $request, $year, $week)
    {
        $date = new Carbon();
        $date->setISODate($year, $week);
        // loads data from the previous and next 2 weeks.
        $start = $date->copy()->subWeeks(2);
        $end = $date->copy()->addWeeks(3);

        // gets just the id, start and end times
        $times = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'start' => $item['start_time']->format('Y-m-d H:i:s'),
                'end' => $item['finish_time']->format('Y-m-d H:i:s'),
            ];
        }, WorkingTime::fromInterval($request->user()->id, $start, $end)->get()->toArray());

        return [
            'interval' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
            ],
            'times' => $times,
        ];
    }

    public function store(WorkingTime $workingTime, CreateWorkingTime $request)
    {
        $workingTime->date = $request->date;
        $workingTime->start_time = $request->start_time;
        $workingTime->finish_time = $request->finish_time;
        $workingTime->provider_id = $request->user()->id;
        $workingTime->save();
        $response = $workingTime->toArray();
        $response['date'] = $response['start_time']->format('Y-m-d');
        $response['start_time'] = $response['start_time']->format('H:i:s');
        $response['finish_time'] = $response['finish_time']->format('H:i:s');
        return $response;
    }

    public function update(WorkingTime $workingTime, UpdateWorkingTime $request)
    {
        $workingTime->date = $request->date;
        $workingTime->start_time = $request->start_time;
        $workingTime->finish_time = $request->finish_time;
        $workingTime->save();
        return $workingTime;
    }

}
