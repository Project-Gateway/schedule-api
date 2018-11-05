<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Client
 *
 * @package App
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $email
 * @property Collection $appointments
*/
class Client
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
        return new self(json_decode($response->getBody(), true));
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        $method = 'get' . ucfirst($name) . 'Attribute';
        return $this->$method();
    }
    
}
