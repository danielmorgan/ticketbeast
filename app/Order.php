<?php

namespace App;

use Facades\App\OrderConfirmationNumber;
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
            'confirmation_number' => OrderConfirmationNumber::generate(),
            'email'               => $email,
            'amount'              => $amount,
        ]);

        $order->tickets()->saveMany($tickets);

        return $order;
    }

    /**
     * Find a ticket from it's confirmation number.
     *
     * @param string $confirmationNumber
     * @return mixed
     */
    public static function findByConfirmationNumber($confirmationNumber)
    {
        return self::where('confirmation_number', $confirmationNumber)
            ->firstOrFail();
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
            'confirmation_number' => $this->confirmation_number,
            'email'               => $this->email,
            'ticket_quantity'     => $this->ticketQuantity(),
            'amount'              => $this->amount,
        ];
    }
}
