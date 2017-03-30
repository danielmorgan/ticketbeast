<?php

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use App\OrderConfirmationNumberGenerator;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\RandomOrderConfirmationNumberGenerator;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var App\Billing\FakePaymentGateway
     */
    private $paymentGateway;

    /**
     * @var Illuminate\Foundation\Testing\TestResponse
     */
    private $response;


    /** @test */
    function customer_can_purchase_tickets_to_a_published_concert()
    {
        $orderConfirmationNumberGenerator = Mockery::mock(OrderConfirmationNumberGenerator::class, [
            'generate' => 'ORDERCONFIRMATION1234',
        ]);

        $this->app->instance(OrderConfirmationNumberGenerator::class, $orderConfirmationNumberGenerator);

        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->states(['published'])
            ->create(['ticket_price' => 3250])
            ->addTickets(3);

        $this->orderTickets($concert, [
            'email'           => 'test@example.com',
            'ticket_quantity' => 3,
            'payment_token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->response->assertStatus(201);
        $this->response->assertJson([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email'               => 'test@example.com',
            'ticket_quantity'     => 3,
            'amount'              => 9750,
        ]);
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor('test@example.com'));
        $this->assertEquals(3, $concert->ordersFor('test@example.com')->first()->ticketQuantity());
    }

    /** @test */
    function customer_cannot_purchase_tickets_to_an_unpublished_concert()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->states(['unpublished'])
            ->create()
            ->addTickets(1);

        $this->orderTickets($concert, [
            'email'           => 'test@example.com',
            'ticket_quantity' => 1,
            'payment_token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->response->assertStatus(404);
        $this->assertEquals(0, $concert->orders->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /** @test */
    function customer_cannot_purchase_more_tickets_than_remain()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->states(['published'])
            ->create()
            ->addTickets(50);

        $this->orderTickets($concert, [
            'email'           => 'test@example.com',
            'ticket_quantity' => 51,
            'payment_token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->response->assertStatus(422);
        $this->assertFalse($concert->hasOrderFor('test@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    function customer_cannot_purchase_tickets_another_customer_is_already_trying_to_purchase()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->states(['published'])
            ->create(['ticket_price' => 1200])
            ->addTickets(3);

        // Queue up Bob's request to happen before Alice's charge is made.
        $this->paymentGateway->beforeFirstCharge(function (PaymentGateway $paymentGateway) use ($concert) {
            $this->orderTickets($concert, [
                'email'           => 'bob@example.com',
                'ticket_quantity' => 2,
                'payment_token'   => $paymentGateway->getValidTestToken(),
            ]);

            $this->response->assertStatus(422);
            $this->assertFalse($concert->hasOrderFor('test@example.com'));
            $this->assertEquals(0, $paymentGateway->totalCharges());
        });

        // Alice's request
        $this->orderTickets($concert, [
            'email'           => 'alice@example.com',
            'ticket_quantity' => 3,
            'payment_token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertEquals(3600, $this->paymentGateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor('alice@example.com'));
        $this->assertEquals(3, $concert->ordersFor('alice@example.com')->first()->ticketQuantity());
    }

    /** @test */
    function an_order_is_not_created_if_payment_fails()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->states(['published'])
            ->create()
            ->addTickets(5);

        $this->orderTickets($concert, [
            'email'           => 'test@example.com',
            'ticket_quantity' => 5,
            'payment_token'   => 'invalid-payment-token',
        ]);

        $this->response->assertStatus(422);
        $this->assertFalse($concert->hasOrderFor('test@example.com'));
        $this->assertEquals(5, $concert->ticketsRemaining());
    }


    /**
     * Validation tests...
     */

    /** @test */
    function email_is_required()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)->states(['published'])->create();

        $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('email');
    }

    /** @test */
    function email_must_be_valid()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)->states(['published'])->create();

        $this->orderTickets($concert, [
            'email'           => 'not-an-email-address',
            'ticket_quantity' => 3,
            'payment_token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('email');
    }

    /** @test */
    function ticket_quantity_is_required()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)->states(['published'])->create();

        $this->orderTickets($concert, [
            'email'         => 'test@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('ticket_quantity');
    }

    /** @test */
    function ticket_quantity_is_at_least_1()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)->states(['published'])->create();

        $this->orderTickets($concert, [
            'email'           => 'test@example.com',
            'ticket_quantity' => 0,
            'payment_token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('ticket_quantity');
    }

    /** @test */
    function payment_token_is_required()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)->states(['published'])->create();

        $this->orderTickets($concert, [
            'email'           => 'test@example.com',
            'ticket_quantity' => 0,
        ]);

        $this->assertValidationError('payment_token');
    }


    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    /**
     * Make POST request to order some tickets.
     *
     * @param \App\Concert $concert
     * @param array        $params
     */
    private function orderTickets(Concert $concert, array $params = [])
    {
        $savedRequest = $this->app['request'];

        $this->response = $this->json('POST', "/concerts/{$concert->id}/orders", $params);

        $this->app['request'] = $savedRequest;
    }

    /**
     * Assert there is a validation error for the passed field.
     *
     * @param string $field
     */
    private function assertValidationError($field)
    {
        $this->response->assertStatus(422);
        $this->assertArrayHasKey($field, $this->response->decodeResponseJson());
    }
}
