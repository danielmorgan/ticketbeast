<?php

use App\Reservation;
use App\Ticket;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReservationTest extends TestCase
{
    /** @test */
    function calculates_the_total_cost()
    {
        $tickets = collect([
            (object) ['price' => 1250],
            (object) ['price' => 1250],
            (object) ['price' => 1250],
        ]);

        $reservation = new Reservation($tickets);

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
        $reservation = new Reservation($tickets);

        $reservation->cancel();

        $tickets->each(function (\Mockery\MockInterface $mock) {
            $mock->shouldHaveReceived('release');
        });
    }
}
