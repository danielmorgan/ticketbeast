<?php

use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use App\Exceptions\PaymentFailedException;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;


    /** @test */
    function charges_with_a_invalid_payment_token_fail()
    {
        $paymentGateway = $this->getPaymentGateway();
        $lastCharge = $paymentGateway->lastCharge();

        try {
            $paymentGateway->charge(2500, 'invalid-payment-token');
        } catch (PaymentFailedException $e) {
            $this->assertCount(0, $paymentGateway->chargesSince($lastCharge));
            return;
        }

        $this->fail('Charging with an invalid payment token did not throw a PaymentFailedException.');
    }


    /**
     * @return \App\Billing\StripePaymentGateway
     */
    protected function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }
}
