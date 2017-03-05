<?php

use App\Billing\StripePaymentGateway;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Token;

/**
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    /**
     * @var \Stripe\Charge|null
     */
    private $lastCharge;


    /** @test */
    function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new StripePaymentGateway(Stripe::$apiKey);

        $paymentGateway->charge(2500, $this->validToken());

        $this->assertCount(1, $this->newCharges());
        $this->assertEquals(2500, $this->lastCharge()->amount);
    }


    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        Stripe::setApiKey(config('services.stripe.secret'));

        $this->lastCharge = $this->lastCharge();
    }

    /**
     * @return \Stripe\Charge|null
     */
    private function lastCharge()
    {
        $charges = Charge::all(['limit' => 1])['data'];

        if (empty($charges)) {
            return null;
        }

        return $charges[0];
    }

    /**
     * @return \Stripe\Collection
     */
    private function newCharges()
    {
        return Charge::all([
            'limit' => 1,
            'ending_before' => $this->lastCharge
                ? $this->lastCharge->id
                : null,
        ])['data'];
    }

    /**
     * @return string
     */
    private function validToken()
    {
        return Token::create([
            'card' => [
                'number'    => '4242424242424242',
                'exp_month' => 1,
                'exp_year'  => date('Y') + 1,
                'cvc'       => '123',
            ],
        ], ['api_key' => config('services.stripe.secret')])->id;
    }
}
