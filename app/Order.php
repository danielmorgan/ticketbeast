<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Create a new order for some tickets.
     *
     * @param \Illuminate\Database\Eloquent\Collection $tickets
     * @param string                                   $email
     * @return static
     */
    public static function forTickets(Collection $tickets, $email)
    {
        $order = self::create([
            'email'  => $email,
            'amount' => $tickets->sum('price'),
        ]);

        $order->tickets()->saveMany($tickets);

        return $order;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    /**
     * Release all tickets and delete the order.
     */
    public function cancel()
    {
        $this->tickets->each(function ($ticket) {
            $ticket->release();
        });

        $this->delete();
    }

    /**
     * Get the number of tickets assigned to an order.
     *
     * @return int
     */
    public function ticketQuantity()
    {
        return $this->tickets->count();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'email'           => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount'          => $this->amount,
        ];
    }
}
