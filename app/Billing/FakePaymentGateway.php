<?php

namespace App\Billing;

use App\Exceptions\PaymentFailedException;
use Illuminate\Support\Collection;

class FakePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4242424242424242';

    /**
     * @var \Illuminate\Support\Collection
     */
    private $charges;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $tokens;

    /**
     * @var \Closure
     */
    private $beforeFirstChargeCallback;

    /**
     * FakePaymentGateway constructor.
     */
    public function __construct()
    {
        $this->charges = new Collection;
        $this->tokens = new Collection;
    }

    /**
     * Sets a callback to be executed before charge(), for testing purposes.
     *
     * @param \Closure $callback
     */
    public function beforeFirstCharge(\Closure $callback)
    {
        $this->beforeFirstChargeCallback = $callback;
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
        if ($this->beforeFirstChargeCallback !== null) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback->__invoke($this);
        }

        if (! $this->tokens->has($token)) {
            throw new PaymentFailedException;
        }

        return $this->charges[] = new Charge([
            'amount'         => $amount,
            'card_last_four' => substr($this->tokens[$token], -4),
        ]);
    }

    /**
     * Get charges made during the callback.
     *
     * @param \Closure $callback
     * @return \Illuminate\Support\Collection
     */
    public function newChargesDuring(\Closure $callback)
    {
        $chargesFrom = $this->charges->count();

        $callback($this);

        return $this->charges
            ->slice($chargesFrom)
            ->reverse()
            ->values();
    }

    /**
     * Sum the charge amounts together.
     *
     * @return int
     */
    public function totalCharges()
    {
        return $this->charges->map->amount()->sum();
    }

    /**
     * Get a valid payment token for use in tests.
     *
     * @return string
     */
    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        $token = 'fake-tok_' . str_random(24);
        $this->tokens[$token] = $cardNumber;

        return $token;
    }
}
