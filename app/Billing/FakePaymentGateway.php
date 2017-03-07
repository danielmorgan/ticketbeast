<?php

namespace App\Billing;

use App\Exceptions\PaymentFailedException;
use Illuminate\Support\Collection;

class FakePaymentGateway implements PaymentGateway
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $charges;

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
     * @throws \App\Exceptions\PaymentFailedException
     */
    public function charge($amount, $token)
    {
        if ($this->beforeFirstChargeCallback !== null) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback->__invoke($this);
        }

        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException;
        }

        $this->charges[] = $amount;
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

        return $this->charges->slice($chargesFrom)->values();
    }

    /**
     * Sum the charge amounts together.
     *
     * @return int
     */
    public function totalCharges()
    {
        return $this->charges->sum();
    }

    /**
     * Get a valid payment token for use in tests.
     *
     * @return string
     */
    public function getValidTestToken()
    {
        return "valid-token";
    }
}
