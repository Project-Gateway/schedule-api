<?php

namespace App\Providers;

use App\Extensions\LoginApiGuard;
use App\Extensions\LoginApiUserProvider;
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

            $http = new HttpClient();

            try {
                $response = $http->get('http://web/login-api/auth/validate', [
                    'headers' => [
                        'accept' => 'application/json',
                        config('auth.applicationHeader') => $appName,
                        'authorization' => $authHeader
                    ]
                ]);
            } catch (\Throwable $e) {
                return null;
            }

            if ($response->getStatusCode() != 200) {
                return null;
            }

            return new User(json_decode($response->getBody()));

        });

    }
}
