<?php
/**
 * Created by PhpStorm.
 * User: dan
 * Date: 04/03/17
 * Time: 18:31
 */

namespace App\Billing;

interface PaymentGateway
{
    /**
     * Return a valid payment provider token for testing.
     *
     * @return string
     */
    public function getValidTestToken();

    /**
     * Add a charge.
     *
     * @param int    $amount
     * @param string $token
     * @throws \App\Exceptions\PaymentFailedException
     */
    public function charge($amount, $token);

    /**
     * Sum the charge amounts together.
     *
     * @return int
     */
    public function totalCharges();
}
