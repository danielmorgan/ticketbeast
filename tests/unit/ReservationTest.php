<?php

use App\Billing\FakePaymentGateway;
use App\Concert;
use App\Reservation;
use App\Ticket;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReservationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function calculates_the_total_cost()
    {
        $tickets = collect([
            (object) ['price' => 1250],
            (object) ['price' => 1250],
            (object) ['price' => 1250],
        ]);
        $reservation = new Reservation($tickets, 'test@example.com');

        $this->assertEquals(3750, $reservation->totalCost());
    }

    /** @test */
    function reserved_tickets_are_released_when_a_reservation_is_cancelled()
    {
        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);
        $reservation = new Reservation($tickets, 'test@example.com');

        $reservation->cancel();

        $tickets->each(function (\Mockery\MockInterface $mock) {
            $mock->shouldHaveReceived('release');
        });
    }

    /** @test */
    function retrieving_the_reservations_tickets()
    {
        $tickets = collect([
            (object) ['price' => 1250],
            (object) ['price' => 1250],
            (object) ['price' => 1250],
        ]);
        $reservation = new Reservation($tickets, 'test@example.com');

        $this->assertEquals($tickets, $reservation->tickets());
    }

    /** @test */
    function retrieving_the_customers_email()
    {
        $reservation = new Reservation(collect(), 'test@example.com');

        $this->assertEquals('test@example.com', $reservation->email());
    }

    /** @test */
    function completing_a_reservation()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200]);
        $tickets = factory(Ticket::class, 3)->create(['concert_id' => $concert->id]);
        $reservation = new Reservation($tickets, 'test@example.com');
        $paymentGateway = new FakePaymentGateway;

        /** @var \App\Order $order */
        $order = $reservation->complete($paymentGateway, $paymentGateway->getValidTestToken());

        $this->assertEquals('test@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(3600, $paymentGateway->totalCharges());
    }
}
