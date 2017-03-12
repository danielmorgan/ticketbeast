<?php

use App\Concert;
use App\Order;
use App\Ticket;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function user_can_view_their_order_confirmation()
    {
        $concert = factory(Concert::class)->create();
        $order = factory(Order::class)->create([
            'amount'              => 8500,
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'card_last_four'      => '1881',
        ]);
        $ticketA = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id'   => $order->id,
            'code'       => 'TICKETCODE123',
        ]);
        $ticketB = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id'   => $order->id,
            'code'       => 'TICKETCODE456',
        ]);

        $response = $this->get('/orders/ORDERCONFIRMATION1234');

        $response->assertStatus(200);
        $response->assertViewHas('order', function ($viewOrder) use ($order) {
            return $viewOrder->id === $order->id;
        });
        $response->assertSee('ORDERCONFIRMATION1234');
        $response->assertSee('£85.00');
        $response->assertSee('**** **** **** 1881');
        $response->assertSee('TICKETCODE123');
        $response->assertSee('TICKETCODE456');
    }
}
