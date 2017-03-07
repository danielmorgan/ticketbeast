<?php

namespace App\Billing;

use App\Exceptions\PaymentFailedException;
use Stripe\Charge;
use Stripe\Error\InvalidRequest;
use Stripe\Token;

class StripePaymentGateway implements PaymentGateway
{
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
     * @throws \App\Exceptions\PaymentFailedException
     */
    public function charge($amount, $token)
    {
        try {
            Charge::create([
                'amount'   => $amount,
                'source'   => $token,
                'currency' => 'gbp',
            ], ['api_key' => $this->apiKey]);
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

        return $this->chargesSince($lastCharge)->pluck('amount');
    }

    /**
     * Get only the newest charge.
     *
     * @return \Stripe\Charge|null
     */
    public function lastCharge()
    {
        $charges = Charge::all(
            ['limit' => 1],
            ['api_key' => $this->apiKey]
        )->data;

        return array_first($charges);
    }

    /**
     * Get all charges made after the a given charge.
     *
     * @param \Stripe\Charge|null $charge
     * @return array|null
     */
    public function chargesSince(Charge $charge = null)
    {
        $charges = Charge::all(
            ['ending_before' => $charge],
            ['api_key' => $this->apiKey]
        )->data;

        return collect($charges);
    }

    /**
     * Get a valid payment token for use in tests.
     *
     * @return string
     */
    public function getValidTestToken()
    {
        return Token::create([
            'card' => [
                'number'    => '4242424242424242',
                'exp_month' => 1,
                'exp_year'  => date('Y') + 1,
                'cvc'       => '123',
            ],
        ], ['api_key' => $this->apiKey])->id;
    }
}
