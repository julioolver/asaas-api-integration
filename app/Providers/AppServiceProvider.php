<?php

namespace App\Providers;

use App\Integrations\Payments\Asaas\{
    AsaasCustomerService,
    AsaasPaymentPixService
};
use App\Integrations\Payments\Contracts\{
    CustomerGatewayInterface,
    PaymentGatewayInterface
};
use App\Repositories\contracts\CustomerRepository;
use App\Repositories\contracts\PaymentRepository;
use App\Repositories\eloquent\EloquentCustomerRepository;
use App\Repositories\eloquent\EloquentPaymentRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CustomerRepository::class, EloquentCustomerRepository::class);
        $this->app->bind(PaymentRepository::class, EloquentPaymentRepository::class);

        $this->app->bind(CustomerGatewayInterface::class, AsaasCustomerService::class);
        $this->app->bind(PaymentGatewayInterface::class, AsaasPaymentPixService::class);
        $this->app->bind(CustomerGatewayInterface::class, AsaasCustomerService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
