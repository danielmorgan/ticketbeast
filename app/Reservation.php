<?php

namespace App;

use Illuminate\Support\Collection;

class Reservation
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $tickets;

    /**
     * @var string
     */
    private $email;

    /**
     * Reservation constructor.
     *
     * @param \Illuminate\Support\Collection $tickets
     * @param string                         $email
     */
    public function __construct(Collection $tickets, $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    /**
     * Tickets getter.
     *
     * @return \Illuminate\Support\Collection
     */
    public function tickets()
    {
        return $this->tickets;
    }

    /**
     * Customer email getter.
     *
     * @return string
     */
    public function email()
    {
        return $this->email;
    }

    /**
     * Create an order.
     *
     * @return \App\Order
     */
    public function complete()
    {
        return Order::forTickets(
            $this->tickets(),
            $this->email(),
            $this->totalCost()
        );
    }

    /**
     * Release tickets.
     *
     * @return void
     */
    public function cancel()
    {
        $this->tickets->each(function (Ticket $ticket) {
            $ticket->release();
        });
    }

    /**
     * Add up the cost of each ticket in the reservation.
     *
     * @return mixed
     */
    public function totalCost()
    {
        return $this->tickets->sum('price');
    }
}
