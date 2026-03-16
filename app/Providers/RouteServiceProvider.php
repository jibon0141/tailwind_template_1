<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Depo\DepoLoginController;



class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers\Frontend')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers\Depo')
                ->prefix('depo')
                ->group(base_path('routes/depo.php'));


            Route::middleware('web')
                ->namespace('App\Http\Controllers\Backend')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers\ChemistHouse')
                ->prefix('chemist-house')
                ->group(base_path('routes/chemist.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers\Employee\Mpo')
                ->prefix('mpo')
                ->group(base_path('routes/employee/mpo.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers\Employee\Asm')
                ->prefix('asm')
                ->group(base_path('routes/employee/asm.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers\Employee\Sm')
                ->prefix('sm')
                ->group(base_path('routes/employee/sm.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers\Employee\Rsm')
                ->prefix('rsm')
                ->group(base_path('routes/employee/rsm.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers\Employee\Nsm')
                ->prefix('nsm')
                ->group(base_path('routes/employee/nsm.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers\Employee\Director')
                ->prefix('director')
                ->group(base_path('routes/employee/director.php'));

            Route::get('/depo/login/{user}', [ DepoLoginController::class, 'login'])
                ->name('depo.magic.login')
                ->middleware('signed');
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
