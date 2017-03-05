<?php

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function can_get_formatted_date()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 8:00pm'),
        ]);

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    /** @test */
    function can_get_formatted_start_time()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00'),
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /** @test */
    function can_get_ticket_price_in_gbp()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750,
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_gbp);
    }

    /** @test */
    function concerts_with_a_published_at_date_are_published()
    {
        $publishedConcertA = factory(Concert::class)->create([
            'published_at' => Carbon::parse('-1 week'),
        ]);

        $publishedConcertB = factory(Concert::class)->create([
            'published_at' => Carbon::parse('+1 week'),
        ]);

        $unpublishedConcert = factory(Concert::class)->create([
            'published_at' => null,
        ]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    /** @test */
    function can_order_concert_tickets()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->create()
            ->addTickets(3);

        $order = $concert->orderTickets('test@example.com', 3);

        $this->assertEquals('test@example.com', $order->email);
        $this->assertEquals(3, $order->tickets()->count());
    }

    /** @test */
    function can_add_tickets()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)->create();

        $concert->addTickets(10);

        $this->assertEquals(10, $concert->ticketsRemaining());
    }

    /** @test */
    function tickets_remaining_does_not_include_tickets_associated_with_an_order()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->create()
            ->addTickets(10);

        $concert->orderTickets('test@example.com', 3);

        $this->assertEquals(7, $concert->ticketsRemaining());
    }

    /** @test */
    function trying_to_purchase_more_tickets_than_remain_throws_an_exception()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->create()
            ->addTickets(10);

        try {
            $concert->orderTickets('test@example.com', 11);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('test@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Order was created even though there were not enough tickets available.');
    }

    /** @test */
    function cannot_order_tickets_that_have_already_been_purchased()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->create()
            ->addTickets(10);

        $concert->orderTickets('alice@example.com', 8);

        try {
            $concert->orderTickets('bob@example.com', 3);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('bob@example.com'));
            $this->assertEquals(2, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Order was created even though there were not enough tickets available.');
    }

    /** @test */
    function can_reserve_available_tickets()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->create()
            ->addTickets(3);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $reservedTickets = $concert->reserveTickets(2);

        $this->assertCount(2, $reservedTickets);
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /** @test */
    function cannot_reserve_tickets_that_have_already_been_reserved()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->create()
            ->addTickets(3);
        $concert->reserveTickets(2);

        try {
            $concert->reserveTickets(2);
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Reserving tickets succeeded even though the tickets were already reserved.');
    }

    /** @test */
    function cannot_reserve_tickets_that_have_already_been_purchased()
    {
        /** @var \App\Concert $concert */
        $concert = factory(Concert::class)
            ->create()
            ->addTickets(3);
        $concert->orderTickets('test@example.com', 2);

        try {
            $concert->reserveTickets(2);
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Reserving tickets succeeded even though the tickets were already sold.');
    }
}
