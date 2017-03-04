<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

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
     * Gets the order total amount.
     *
     * @return int
     */
    public function total()
    {
        return $this->concert->ticket_price * $this->ticketQuantity();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'email'           => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount'          => $this->total(),
        ];
    }
}
