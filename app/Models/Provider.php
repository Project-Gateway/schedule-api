<?php

namespace App\Models;

/**
 * Class Employee
 *
 * @package App
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $email
 * @property Service[] $services
 */
class Provider
{

    protected $id;
    protected $first_name;
    protected $last_name;
    protected $phone;
    protected $email;

    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'];
        $this->first_name = $attributes['first_name'];
        $this->last_name = $attributes['last_name'];
        $this->phone = $attributes['phone'];
        $this->email = $attributes['emails'][0]['email'];
    }

    public static function find($id)
    {
        $response = app('auth')->user()->loginApi->get("users/{$id}");
        return new Provider(json_decode($response->getBody(), true));
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        $method = 'get' . ucfirst($name) . 'Attribute';
        return $this->$method();
    }

    public function getServicesAttribute()
    {
        return Service::join('provider_service', 'provider_service.service_id', '=', 'services.id')
            ->where(['provider_service.provider_id' => $this->id])
            ->get();
    }

    public function provides_service(int $serviceId)
    {
        return Service::join('provider_service', 'provider_service.service_id', '=', 'services.id')
            ->where(['provider_service.provider_id' => $this->id])
            ->where(['provider_service.service_id' => $serviceId])
            ->exists();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
        ];
    }

    public function working_times()
    {
        return WorkingTime::where(['provider_id' => $this->id]);
    }

    public function appointments()
    {
        return Appointment::where(['provider_id' => $this->id]);
    }

    public function is_working($date)
    {
        return $this->working_hours->where('date', '=', $date)->first();
    }

    public function service_info($service)
    {
        return $this->services()->where('service_id', $service)->first();
    }

}
