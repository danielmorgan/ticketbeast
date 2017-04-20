<?php

namespace App\Providers;

use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use App\HashidsTicketCodeGenerator;
use App\OrderConfirmationNumberGenerator;
use App\RandomOrderConfirmationNumberGenerator;
use App\TicketCodeGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(StripePaymentGateway::class, function () {
            return new StripePaymentGateway(config('services.stripe.secret'));
        });

        $this->app->bind(HashidsTicketCodeGenerator::class, function () {
            return new HashidsTicketCodeGenerator(config('app.key'));
        });

        $this->app->bind(OrderConfirmationNumberGenerator::class, RandomOrderConfirmationNumberGenerator::class);
        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
        $this->app->bind(TicketCodeGenerator::class, HashidsTicketCodeGenerator::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
