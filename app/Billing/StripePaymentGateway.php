<?php

namespace App\Billing;

use App\Exceptions\PaymentFailedException;
use Stripe\Charge;
use Stripe\Error\InvalidRequest;

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
}
