<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        $this->configureUrl();
//        $this->configureVite();
//        $this->configureModels();
//        $this->configureCommands();

        RateLimiter::for('auth', function (Request $request) {
            return [
                Limit::perMinutes(1, 5)->response(function (Request $request, array $headers) {
                    return returnErrorJson('Error, too many requests. please slow down. You must wait '
                        . $headers['Retry-After'] . ' seconds before trying again',
                        429);
                })->by($request->ip()),
                Limit::perMinutes(5, 9)->response(function (Request $request, array $headers) {
                    return returnErrorJson(
                        'Error, too many requests. please slow down. You must wait '
                        . $headers['Retry-After'] . ' seconds before trying again',
                        429);
                })->by($request->ip()),
            ];
//            return [
//                Limit::perMinute(10)->by('minute:'.$request->user()->id),
//                Limit::perDay(1000)->by('day:'.$request->user()->id),
//            ];

//            return [
//                Limit::perMinute(500),
//                Limit::perMinute(3)->by($request->input('email')),
//            ];

//            return $request->user()
//                ? Limit::perMinute(100)->by($request->user()->id)
//                : Limit::perMinute(10)->by($request->ip());

//            return $request->user()->vipCustomer()
//                ? Limit::none()
//                : Limit::perMinute(100)->by($request->ip());
        });
    }

    private function configureUrl(): void
    {
        if (app()->isProduction())
            URL::forceScheme('https');

    }

    private function configureModels(): void
    {
        Model::shouldBeStrict();
//        Model::unguard();
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());
    }

    private function configureVite(): void
    {
        Vite::usePrefetchStrategy('aggressive');
    }
}
