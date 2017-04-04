<?php

namespace App\Billing;

use App\Exceptions\PaymentFailedException;
use Stripe\Error\InvalidRequest;
use Stripe\Token;

class StripePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4242424242424242';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * StripePaymentGateway constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Add a charge.
     *
     * @param int    $amount
     * @param string $token
     * @return \App\Billing\Charge
     * @throws \App\Exceptions\PaymentFailedException
     */
    public function charge($amount, $token)
    {
        try {
            $stripeCharge = \Stripe\Charge::create([
                'amount'   => $amount,
                'source'   => $token,
                'currency' => 'gbp',
            ], ['api_key' => $this->apiKey]);

            return new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['source']['last4'],
            ]);
        } catch (InvalidRequest $e) {
            throw new PaymentFailedException;
        }
    }

    /**
     * Get charges made during the callback.
     *
     * @param \Closure $callback
     * @return \Illuminate\Support\Collection
     */
    public function newChargesDuring(\Closure $callback)
    {
        $lastCharge = $this->lastCharge();

        $callback($this);

        return $this->chargesSince($lastCharge)->map(function ($stripeCharge) {
            return new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['source']['last4'],
            ]);
        });
    }

    /**
     * Get only the newest charge.
     *
     * @return \Stripe\Charge|null
     */
    public function lastCharge()
    {
        $charges = \Stripe\Charge::all(
            ['limit' => 1],
            ['api_key' => $this->apiKey]
        )->data;

        return array_first($charges);
    }

    /**
     * Get all charges made after the a given charge.
     *
     * @param \Stripe\Charge|null $charge
     * @return \Illuminate\Support\Collection
     */
    public function chargesSince(\Stripe\Charge $charge = null)
    {
        $charges = \Stripe\Charge::all(
            ['ending_before' => $charge],
            ['api_key' => $this->apiKey]
        )->data;

        return collect($charges);
    }

    /**
     * Get a valid payment token for use in tests.
     *
     * @param string $cardNumber
     * @return string
     */
    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        return Token::create([
            'card' => [
                'number'    => $cardNumber,
                'exp_month' => 1,
                'exp_year'  => date('Y') + 1,
                'cvc'       => '123',
            ],
        ], ['api_key' => $this->apiKey])->id;
    }
}
