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
     * Get charges made during the callback.
     *
     * @param \Closure $callback
     * @return \Illuminate\Support\Collection
     */
    public function newChargesDuring(\Closure $callback);

    /**
     * Get a valid payment token for use in tests.
     *
     * @return string
     */
    public function getValidTestToken();
}
