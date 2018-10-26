<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use GuzzleHttp\Client as HttpClient;

/**
 * Class WorkingHour
 *
 * @package App
 * @property sting $provider_id
 * @property Carbon $date
 * @property Carbon $start_time
 * @property Carbon $finish_time
 * @property Provider $provider
*/
class WorkingTime extends Model
{
    use SoftDeletes;

    protected $fillable = ['date', 'start_time', 'finish_time', 'provider_id'];
    
    protected $dates = ['date'];

    public function getStartTimeAttribute()
    {
        $time = $this->date->format('Y-m-d ') . $this->attributes['start_time'];
        return Carbon::createFromFormat('Y-m-d H:i:s', $time);
    }

    public function getFinishTimeAttribute()
    {
        $time = $this->date->format('Y-m-d ') . $this->attributes['finish_time'];
        return Carbon::createFromFormat('Y-m-d H:i:s', $time);
    }

    public function getProviderAttribute()
    {
        $response = app('auth')->user()->loginApi->get("users/{$this->provider_id}");
        $provider = new Provider(json_decode($response->getBody(), true));
        return $provider;
    }

    public function scopeFromInterval($query, string $userId, Carbon $start, Carbon $end)
    {
        return $query
            ->where('provider_id', $userId)
            ->whereBetween('date', [$start, $end]);
    }
    
}
