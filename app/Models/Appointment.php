<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Appointment
 *
 * @package App
 * @property string $client_id
 * @property int $provider_id
 * @property int $service_id
 * @property Carbon $start_time
 * @property Carbon $finish_time
 * @property string $comments
 * @property Provider $provider
 * @property Service $service
*/
class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = ['start_time', 'finish_time', 'comments', 'client_id', 'provider_id'];

    protected $dates = [
        'start_time',
        'finish_time',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = $value . ':00';
    }
    
    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id')->withTrashed();
    }
	
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id')->withTrashed();
    }	
    
}
