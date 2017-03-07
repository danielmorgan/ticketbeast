<?php

use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use App\Exceptions\PaymentFailedException;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Stripe\Charge;
use Stripe\Stripe;

/**
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    /**
     * @var \Stripe\Charge|null
     */
    private $lastCharge;

    /**
     * @var \App\Billing\StripePaymentGateway
     */
    private $paymentGateway;


    /** @test */
    function charges_with_a_valid_payment_token_are_successful()
    {
        $charge =  $this->paymentGateway->lastCharge();

        $newCharges = $this->paymentGateway->newChargesDuring(function (PaymentGateway $paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        });

        $this->assertCount(1, $this->paymentGateway->chargesSince($charge));
        $this->assertEquals(2500, $newCharges->sum());
    }

    /** @test */
    function charges_with_a_invalid_payment_token_fail()
    {
        try {
            $this->paymentGateway->charge(2500, 'invalid-payment-token');
        } catch (PaymentFailedException $e) {
            $this->assertCount(0, $this->newCharges());
            return;
        }

        $this->fail('Charging with an invalid payment token did not throw a PaymentFailedException.');
    }


    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        Stripe::setApiKey(config('services.stripe.secret'));

        $this->paymentGateway = new StripePaymentGateway(Stripe::$apiKey);

        $this->lastCharge = $this->lastCharge();
    }

    /**
     * @return \Stripe\Charge|null
     */
    private function lastCharge()
    {
        $charges = Charge::all(['limit' => 1])['data'];

        if (empty($charges)) {
            return null;
        }

        return $charges[0];
    }

    /**
     * @return \Stripe\Collection
     */
    private function newCharges()
    {
        return Charge::all([
            'limit' => 1,
            'ending_before' => $this->lastCharge
                ? $this->lastCharge->id
                : null,
        ])['data'];
    }
}
