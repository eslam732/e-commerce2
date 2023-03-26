<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

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
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

                Route::middleware('api')
                ->prefix('auth')
                ->group(base_path('routes/user/auth.php'));

                Route::middleware('api')
                ->middleware('auth:api')
                ->prefix('user')
                ->group(base_path('routes/user/user.php'));

                Route::middleware('api')
                ->middleware('auth:api')
                ->prefix('product')
                ->group(base_path('routes/product/product.php'));

                Route::middleware('api')
                ->middleware('auth:api')
                ->prefix('category')
                ->group(base_path('routes/product/category.php'));

                Route::middleware('api')
                ->middleware('auth:api','admin')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
