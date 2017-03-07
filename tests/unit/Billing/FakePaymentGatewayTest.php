<?php

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Exceptions\PaymentFailedException;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FakePaymentGatewayTest extends TestCase
{
    /**
     * @var \App\Billing\FakePaymentGateway
     */
    private $paymentGateway;


    /** @test */
    function can_fetch_charges_created_during_a_callback()
    {
        $this->paymentGateway->charge(2000, $this->paymentGateway->getValidTestToken());
        $this->paymentGateway->charge(3000, $this->paymentGateway->getValidTestToken());

        $newCharges = $this->paymentGateway->newChargesDuring(function (PaymentGateway $paymentGateway) {
            $this->paymentGateway->charge(4000, $this->paymentGateway->getValidTestToken());
            $this->paymentGateway->charge(5000, $this->paymentGateway->getValidTestToken());
        });

        $this->assertCount(2, $newCharges);
        $this->assertEquals([4000, 5000], $newCharges->all());
    }

    /** @test */
    function charges_with_a_valid_payment_token_are_successful()
    {
        $newCharges = $this->paymentGateway->newChargesDuring(function (PaymentGateway $paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
            $paymentGateway->charge(1000, $paymentGateway->getValidTestToken());
        });

        $this->assertCount(2, $newCharges);
        $this->assertEquals(3500, $newCharges->sum());
    }

    /** @test */
    function charges_with_a_invalid_payment_token_fail()
    {
        try {
            $this->paymentGateway->charge(2500, 'invalid-payment-token');
        } catch (PaymentFailedException $e) {
            return;
        }

        $this->fail('Charge succeeded even though payment token was invalid.');
    }

    /** @test */
    function running_a_hook_before_the_first_charge()
    {
        $timesCallbackRan = 0;

        $this->paymentGateway->beforeFirstCharge(function (PaymentGateway $paymentGateway) use (&$timesCallbackRan) {
            $timesCallbackRan++;
            $this->paymentGateway->charge(2500, $this->paymentGateway->getValidTestToken());
            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });

        $this->paymentGateway->charge(2500, $this->paymentGateway->getValidTestToken());
        $this->assertEquals(1, $timesCallbackRan);
        $this->assertEquals(5000, $this->paymentGateway->totalCharges());
    }


    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->paymentGateway = new FakePaymentGateway;
    }
}
