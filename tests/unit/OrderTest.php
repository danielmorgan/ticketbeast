<?php

use App\Concert;
use App\Order;
use App\Reservation;
use App\Ticket;
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
    function creating_an_order_from_a_reservation()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200]);
        $tickets = factory(Ticket::class, 3)->create(['concert_id' => $concert->id]);
        $reservation = new Reservation($tickets, 'test@example.com');

        /** @var \App\Order $order */
        $order = Order::fromReservation($reservation);

        $this->assertEquals('test@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
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
