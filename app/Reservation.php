<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;

class Reservation
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $tickets;

    public function __construct(Collection $tickets)
    {
        $this->tickets = $tickets;
    }

    public function totalCost()
    {
        return $this->tickets->sum('price');
    }
}
