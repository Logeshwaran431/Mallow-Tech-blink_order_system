<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // @money($amount) prints a formatted amount, e.g. ₹1,23,456.78.
        Blade::directive('money', function ($expression) {
            return "<?php echo e(\\App\\Support\\Money::format($expression)); ?>";
        });
    }
}
