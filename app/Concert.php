<?php

namespace App;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Return date in preferred format.
     *
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    /**
     * Return time in preferred format.
     *
     * @return string
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    /**
     * Return time in preferred format.
     *
     * @return string
     */
    public function getTicketPriceInGbpAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }

    /**
     * Scope the query to only published concerts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * Create an order and some tickets.
     *
     * @param string $email
     * @param int $ticketQuantity
     * @return \App\Order
     * @throws \App\Exceptions\NotEnoughTicketsException
     */
    public function orderTickets($email, $ticketQuantity = 1)
    {
        $tickets = $this->findTickets($ticketQuantity);

        return $this->createOrder($email, $tickets);
    }

    /**
     * Get some tickets if available.
     *
     * @param int $ticketQuantity
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \App\Exceptions\NotEnoughTicketsException
     */
    public function findTickets($ticketQuantity)
    {
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();

        if ($tickets->count() < $ticketQuantity) {
            throw new NotEnoughTicketsException;
        }

        return $tickets;
    }

    /**
     * Create an order and attach some tickets to it.
     *
     * @param string $email
     * @param \Illuminate\Database\Eloquent\Collection $tickets
     * @return \App\Order
     */
    public function createOrder($email, Collection $tickets)
    {
        /** @var Order $order */
        $order = $this->orders()->create([
            'email'  => $email,
            'amount' => $tickets->count() * $this->ticket_price,
        ]);

        $order->tickets()->saveMany($tickets);

        return $order;
    }

    /**
     * Add empty tickets that can be purchased.
     *
     * @param int $quantity
     * @return $this
     */
    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }

        return $this;
    }

    /**
     * Return number of tickets that remain unpurchased.
     *
     * @return int
     */
    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }

    /**
     * Check if there are any order for a given email.
     *
     * @param string $customerEmail
     * @return bool
     */
    public function hasOrderFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->count() > 0;
    }

    /**
     * Get all orders for a given email.
     *
     * @param string $customerEmail
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function ordersFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->get();
    }
}
