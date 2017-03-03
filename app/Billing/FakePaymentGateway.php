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
    public function charge($amount, $token)
    {
        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException;
        }

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
