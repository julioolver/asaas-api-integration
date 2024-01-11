<?php

namespace App\Providers;

use App\Repositories\contracts\CustomerRepository;
use App\Repositories\eloquent\EloquentCustomerRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CustomerRepository::class, EloquentCustomerRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
