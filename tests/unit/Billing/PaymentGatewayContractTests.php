<?php

use App\Billing\PaymentGateway;
use App\Exceptions\PaymentFailedException;

trait PaymentGatewayContractTests
{
    /** @test */
    function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = $this->getPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function (PaymentGateway $paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
            $paymentGateway->charge(1000, $paymentGateway->getValidTestToken());
        });

        $this->assertCount(2, $newCharges);
        $this->assertEquals(3500, $newCharges->sum());
    }

    /** @test */
    function charges_with_a_invalid_payment_token_fail()
    {
        $paymentGateway = $this->getPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function (PaymentGateway $paymentGateway) {
            try {
                $paymentGateway->charge(2500, 'invalid-test-token');
            } catch (PaymentFailedException $e) {
                return;
            }

            $this->fail('Charge succeeded even though payment token was invalid.');
        });

        $this->assertCount(0, $newCharges);
    }

    /** @test */
    function can_fetch_charges_created_during_a_callback()
    {
        $paymentGateway = $this->getPaymentGateway();

        $paymentGateway->charge(2000, $paymentGateway->getValidTestToken());
        $paymentGateway->charge(3000, $paymentGateway->getValidTestToken());

        $newCharges = $paymentGateway->newChargesDuring(function (PaymentGateway $paymentGateway) {
            $paymentGateway->charge(4000, $paymentGateway->getValidTestToken());
            $paymentGateway->charge(5000, $paymentGateway->getValidTestToken());
        });

        $this->assertCount(2, $newCharges);
        $this->assertEquals([5000, 4000], $newCharges->all());
    }


    /**
     * @return PaymentGateway
     */
    abstract protected function getPaymentGateway();
}
