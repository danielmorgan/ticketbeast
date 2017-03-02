<?php

namespace App\Billing;

use Illuminate\Support\Collection;

class FakePaymentGateway implements PaymentGateway
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $charges;

    /**
     * FakePaymentGateway constructor.
     */
    public function __construct()
    {
        $this->charges = new Collection;
    }

    /**
     * @return string
     */
    public function getValidTestToken()
    {
        return "valid-token";
    }

    /**
     *
     */
    public function charge($amount)
    {
        $this->charges[] = $amount;
    }

    /**
     * @return int
     */
    public function totalCharges()
    {
        return $this->charges->sum();
    }
}
