<?php

namespace App;

use App\Billing\Charge;
use App\Facades\OrderConfirmationNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Order
 *
 * @property int $id
 * @property string $confirmation_number
 * @property string $email
 * @property int $amount
 * @property string $card_last_four
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Concert $concert
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ticket[] $tickets
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereCardLastFour($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereConfirmationNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
     * @param \App\Billing\Charge            $charge
     * @return static
     */
    public static function forTickets(Collection $tickets, $email, Charge $charge)
    {
        $order = self::create([
            'confirmation_number' => OrderConfirmationNumber::generate(),
            'email'               => $email,
            'amount'              => $charge->amount(),
            'card_last_four'      => $charge->cardLastFour(),
        ]);

        $tickets->each->claimFor($order);

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
            'amount'              => $this->amount,
            'tickets'             => $this->tickets->map(function (Ticket $ticket) {
                return ['code' => $ticket->code];
            })->all(),
        ];
    }
}
