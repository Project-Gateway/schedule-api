<?php

namespace App\Providers;

use App\Models\User;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        app('auth')->viaRequest('login-api', function ($request) {

            // The Authorization header
            $authHeader = $request->header('authorization');

            // The application header
            $appName = $request->header(config('auth.applicationHeader'));

            $http = new HttpClient([
                'base_uri' => 'http://web/login-api/',
                'headers' => [
                    'accept' => 'application/json',
                    config('auth.applicationHeader') => $appName,
                    'authorization' => $authHeader
                ]
            ]);

            try {
                $response = $http->get('auth/validate');
            } catch (\Throwable $e) {
                return null;
            }

            if ($response->getStatusCode() != 200) {
                return null;
            }

            return new User(json_decode($response->getBody()), $http);

        });

    }
}
