<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleRequest;
use App\Models\Appointment;
use App\Models\Provider;
use App\Models\WorkingTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Admin\StoreAppointmentsRequest;
use App\Http\Requests\Admin\UpdateAppointmentsRequest;
use Illuminate\Support\Facades\Auth;

class AppointmentsController extends Controller
{

    /**
     * Returns the available services and providers based on a date
     *
     * @param $date
     * @return array
     */
    public function servicesByDate($date)
    {

        $params = explode('-', $date);
        $date = Carbon::createMidnightDate(...$params);

        $workingTimes = WorkingTime::whereDate('date', $date->format('Y-m-d'))->get();

        $output = [];
        foreach ($workingTimes as $workingTime) {
            foreach ($workingTime->provider->services as $service) {
                if (empty($output[$service->name])) {
                    $output[$service->name] = $service->toArray();
                }
                $provider = $workingTime->provider->toArray();
                unset($provider['services']);
                $output[$service->name]['providers'][$provider['id']] = $provider;
            }
        }

        return response($output);
    }

    /**
     * Returns the available time slots for a provider, based on a date
     *
     * @param string $providerId
     * @param $date
     * @return array
     */
    public function availability($date, string $providerId = null)
    {
        return response($this->findTimeSlots($date, $providerId));
    }

    protected function findTimeSlots($date, string $providerId)
    {
        $params = explode('-', $date);
        $date = Carbon::createMidnightDate(...$params);

        $provider = Provider::find($providerId);

        $workingTimes = $provider->working_times()->whereDate('date', $date->format('Y-m-d'))->get();

        $appointments = $provider->appointments()->whereDate('start_time', $date->format('Y-m-d'))->get();

        $appointmentDuration = config('appointments.duration');
        $appointmentInterval = config('appointments.interval');

        $timeSlots = [];

        // find the base time slots
        /** @var WorkingTime $workingTime */
        foreach ($workingTimes as $workingTime) {
            $start = $workingTime->start_time;
            $end = $workingTime->finish_time;
            while ($start < $end) {
                if ($start->diffInMinutes($end) > $appointmentDuration) {
                    $timeSlots[] = $start->format('H:i');
                }
                $start->addMinutes($appointmentInterval);
            }
        }

        // find the unavailable time slots
        /** @var Appointment $appointment */
        foreach ($appointments as $appointment) {
            if (($key = array_search($appointment->start_time->format('H:i'), $timeSlots)) !== false) {
                unset($timeSlots[$key]);
            }
        }

        // use array_values to ensure that it will be interpreted as an array (not object) on the frontend
        return array_values($timeSlots);
    }

    /**
     * Schedule a time for the logged client
     */
    public function schedule(ScheduleRequest $request)
    {
        $provider = Provider::find($request->provider_id);

        [$date, $time] = explode(' ', $request->time);

        if (array_search($time, $this->findTimeSlots($date, $provider->id)) === false) {
            return response(['message' => 'The selected time is unavailable'], 410);
        }

        $appointment = new Appointment();
        $appointment->client_id = Auth::id();
        $appointment->provider_id = $provider->id;
        $appointment->service_id = (int) $request->service_id;
        $appointment->start_time = $request->time;
        $appointment->finish_time = $appointment->start_time->addMinutes(config('appointments.interval'));
        $appointment->comments = $request->comments;
        $appointment->save();

        return response(null, 201);
    }

    /**
     * Display a listing of Appointment.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Appointment::all();
    }

    public function byWeek(Request $request, $year, $week)
    {
        $date = new Carbon();
        $date->setISODate($year, $week);
        // loads data from the previous and next 2 weeks.
        $start = $date->copy()->subWeeks(2);
        $end = $date->copy()->addWeeks(3);

        // gets just the id, service, provider, start and end times
        $times = [];
        $appointments = Appointment::with(['service'])
            ->fromInterval($start, $end)
            ->where('client_id', $request->user()->id)
            ->get();
        foreach ($appointments as $appointment) {
            $provider = $appointment->provider;
            $times[] = [
                'id' => $appointment->id,
                'service' => $appointment->service->name,
                'provider' => "$provider->first_name $provider->last_name",
                'start' => $appointment->start_time->format('Y-m-d H:i:s'),
                'end' => $appointment->finish_time->format('Y-m-d H:i:s'),
            ];
        }

        return [
            'interval' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
            ],
            'times' => $times,
        ];

        return Appointment::where('client_id', $request->user()->id)->get();
    }

    public function byWeekInternal(Request $request, $year, $week)
    {
        $date = new Carbon();
        $date->setISODate($year, $week);
        // loads data from the previous and next 2 weeks.
        $start = $date->copy()->subWeeks(2);
        $end = $date->copy()->addWeeks(3);

        // gets just the id, service, customer, start and end times
        $times = [];
        $appointments = Appointment::with(['service'])
            ->fromInterval($start, $end)
            ->where('provider_id', $request->user()->id)
            ->get();
        foreach ($appointments as $appointment) {
            $client = $appointment->client;
            $times[] = [
                'id' => $appointment->id,
                'service' => $appointment->service->name,
                'customer' => "$client->first_name $client->last_name",
                'start' => $appointment->start_time->format('Y-m-d H:i:s'),
                'end' => $appointment->finish_time->format('Y-m-d H:i:s'),
            ];
        }

        return [
            'interval' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
            ],
            'times' => $times,
        ];

        return Appointment::where('client_id', $request->user()->id)->get();
    }

    /**
     * Store a newly created Appointment in storage.
     *
     * @param StoreAppointmentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAppointmentsRequest $request)
    {
        if (! Gate::allows('appointment_create')) {
            return abort(401);
        }
		$employee = \App\Provider::find($request->employee_id);
		$working_hours = \App\WorkingTime::where('employee_id', $request->employee_id)->whereDay('date', '=', date("d", strtotime($request->date)))->whereTime('start_time', '<=', date("H:i", strtotime("".$request->starting_hour.":".$request->starting_minute.":00")))->whereTime('finish_time', '>=', date("H:i", strtotime("".$request->finish_hour.":".$request->finish_minute.":00")))->get();
		if(!$employee->provides_service($request->service_id)) return redirect()->back()->withErrors("This employee doesn't provide your selected service")->withInput();
        if($working_hours->isEmpty()) return redirect()->back()->withErrors("This employee isn't working at your selected time")->withInput();
		$appointment = new Appointment;
		$appointment->client_id = $request->client_id;
		$appointment->employee_id = $request->employee_id;
		$appointment->start_time = "".$request->date." ".$request->starting_hour .":".$request->starting_minute.":00";
		$appointment->finish_time = "".$request->date." ".$request->finish_hour .":".$request->finish_minute.":00";
		$appointment->comments = $request->comments;
		$appointment->save();



        return redirect()->route('admin.appointments.index');
    }

    /**
     * Update Appointment in storage.
     *
     * @param UpdateAppointmentsRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAppointmentsRequest $request, $id)
    {
        if (! Gate::allows('appointment_edit')) {
            return abort(401);
        }
        $appointment = Appointment::findOrFail($id);
        $appointment->update($request->all());



        return redirect()->route('admin.appointments.index');
    }


    /**
     * Display Appointment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('appointment_view')) {
            return abort(401);
        }
        $relations = [
            'clients' => \App\Client::get()->pluck('first_name', 'id')->prepend('Please select', ''),
            'employees' => \App\Provider::get()->pluck('first_name', 'id')->prepend('Please select', ''),
        ];

        $appointment = Appointment::findOrFail($id);

        return view('admin.appointments.show', compact('appointment') + $relations);
    }


    /**
     * Remove Appointment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('appointment_delete')) {
            return abort(401);
        }
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Delete all selected Appointment at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('appointment_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Appointment::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
