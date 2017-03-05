<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Order extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Create a new order for some tickets.
     *
     * @param \Illuminate\Support\Collection $tickets
     * @param string                         $email
     * @param int                            $amount
     * @return static
     */
    public static function forTickets(Collection $tickets, $email, $amount)
    {
        $order = self::create([
            'email'  => $email,
            'amount' => $amount,
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
