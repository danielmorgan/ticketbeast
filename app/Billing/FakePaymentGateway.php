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
     * Return a valid payment provider token for testing.
     *
     * @return string
     */
    public function getValidTestToken()
    {
        return "valid-token";
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
     * Sum the charge amounts together.
     *
     * @return int
     */
    public function totalCharges()
    {
        return $this->charges->sum();
    }
}
