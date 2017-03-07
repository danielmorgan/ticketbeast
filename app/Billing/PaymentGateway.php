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
     * Add a charge.
     *
     * @param int    $amount
     * @param string $token
     * @throws \App\Exceptions\PaymentFailedException
     */
    public function charge($amount, $token);

    /**
     * Get a valid payment token for use in tests.
     *
     * @return string
     */
    public function getValidTestToken();
}
