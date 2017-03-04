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
     * Reservation constructor.
     *
     * @param \Illuminate\Support\Collection $tickets
     */
    public function __construct(Collection $tickets)
    {
        $this->tickets = $tickets;
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
