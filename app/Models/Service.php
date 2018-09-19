<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Service
 * @package App\Models
 * @property Collection $providers
 */
class Service extends Model
{
    protected $fillable = ['name', 'price'];

	public function providers()
    {
        return $this->belongsToMany(Provider::class);
    }
}
