<?php

use App\Reservation;
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
}
