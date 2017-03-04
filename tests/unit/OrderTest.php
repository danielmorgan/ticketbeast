<?php

use App\Concert;
use App\Order;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function tickets_are_released_when_an_order_is_cancelled()
    {
        $concert = factory(Concert::class)
            ->create()
            ->addTickets(10);

        $order = $concert->orderTickets('test@example.com', 5);
        $this->assertEquals(5, $concert->ticketsRemaining());
        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());
        $this->assertNull(Order::find($order->id));
    }

    /** @test */
    function converting_to_an_array()
    {
        $concert = factory(Concert::class)
            ->create(['ticket_price' => 1200])
            ->addTickets(5);

        $order = $concert->orderTickets('test@example.com', 5);
        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'test@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000,
        ], $result);
    }

    /** @test */
    function can_calculate_total_amount()
    {
        $concert = factory(Concert::class)
            ->create(['ticket_price' => 550])
            ->addTickets(3);

        $order = $concert->orderTickets('test@example.com', 3);
        $total = $order->total();

        $this->assertEquals(1650, $total);
    }
}
