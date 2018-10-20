<?php

namespace App\Models;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class User
 * @package App\Models
 * @property $firstName
 * @property $lastName
 * @property $phone
 * @property $email
 * @property $role
 * @property $loginApi
 */
class User implements Authenticatable
{

    protected $id;
    protected $firstName;
    protected $lastName;
    protected $phone;
    protected $email;
    protected $role;
    protected $loginApi;

    public function __construct($jwtPayload, HttpClient $loginApi)
    {
        $this->id = $jwtPayload->sub;
        $this->firstName = $jwtPayload->first_name;
        $this->lastName = $jwtPayload->last_name;
        $this->phone = $jwtPayload->phone;
        $this->email = $jwtPayload->emails[0];
        $this->role = $jwtPayload->role;
        $this->loginApi = $loginApi;

    }

    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        // TODO: Implement getAuthPassword() method.
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        // TODO: Implement getRememberToken() method.
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // TODO: Implement setRememberToken() method.
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        // TODO: Implement getRememberTokenName() method.
    }
}
