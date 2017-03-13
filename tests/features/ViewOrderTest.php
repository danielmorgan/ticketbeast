<?php

use App\Concert;
use App\Order;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function user_can_view_their_order_confirmation()
    {
        $this->disableExceptionHandling();
        $concert = factory(Concert::class)->create([
            'title'                  => 'The Red Chord',
            'subtitle'               => 'with Animosity and Lethargy',
            'date'                   => Carbon::parse('March 12, 2017 8:00pm'),
            'ticket_price'           => 3250,
            'venue'                  => 'The Mosh Pit',
            'venue_address'          => '123 Example Lane',
            'city'                   => 'Laraville',
            'state'                  => 'ON',
            'zip'                    => '17916',
            'additional_information' => 'For tickets, call (555) 555-5555.',
        ]);
        $order = factory(Order::class)->create([
            'amount'              => 8500,
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'card_last_four'      => '1881',
            'email'               => 'test@example.com',
        ]);
        $ticketA = factory(\App\Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id'   => $order->id,
            'code'       => 'TICKETCODE123',
        ]);
        $ticketB = factory(\App\Ticket::class)->create([
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

        $response->assertSee('The Red Chord');
        $response->assertSee('with Animosity and Lethargy');
        $response->assertSee('The Mosh Pit');
        $response->assertSee('123 Example Lane');
        $response->assertSee('Laraville, ON 17916');
        $response->assertSee('test@example.com');

        $response->assertSee('2017-03-12 20:00');
    }
}
