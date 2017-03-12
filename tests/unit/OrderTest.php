<?php

use App\Concert;
use App\Order;
use App\Reservation;
use App\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function creating_an_order_from_tickets_email_and_amount()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->create()
            ->addTickets(5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order = Order::forTickets(
            $concert->findTickets(3),
            'test@example.com',
            7500
        );

        $this->assertEquals('test@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(7500, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    /** @test */
    function retrieving_an_order_by_confirmation_number()
    {
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
        ]);

        $foundOrder = Order::findByConfirmationNumber('ORDERCONFIRMATION1234');

        $this->assertEquals($order->id, $foundOrder->id);
    }

    /** @test */
    function retrieving_a_nonexistant_order_by_confirmation_number()
    {
        try {
            Order::findByConfirmationNumber('NONEXISTANTCONFIRMATIONNUMBER');
        } catch (ModelNotFoundException $e) {
            return;
        }

        $this->fail('No matching order was found for the specified confirmation number, but an exception was not thrown.');
    }

    /** @test */
    function converting_to_an_array()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->create(['ticket_price' => 1200])
            ->addTickets(5);

        $order = $concert->orderTickets('test@example.com', 5);
        $result = $order->toArray();

        $this->assertEquals([
            'email'           => 'test@example.com',
            'ticket_quantity' => 5,
            'amount'          => 6000,
        ], $result);
    }
}
