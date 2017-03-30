<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Ticket
 *
 * @property int $id
 * @property int $concert_id
 * @property int $order_id
 * @property string $reserved_at
 * @property string $code
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Concert $concert
 * @property-read int $price
 * @method static \Illuminate\Database\Query\Builder|\App\Ticket available()
 * @method static \Illuminate\Database\Query\Builder|\App\Ticket whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Ticket whereConcertId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Ticket whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Ticket whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Ticket whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Ticket whereReservedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Ticket whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ticket extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|mixed
     */
    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    /**
     * Get price of a ticket based on the concert's set price.
     *
     * @return int
     */
    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }

    /**
     * Only return tickets that haven't been purchased.
     *
     * @param $query
     * @return mixed
     */
    public function scopeAvailable($query)
    {
        return $query->whereNull('reserved_at')->whereNull('order_id');
    }

    /**
     * Reserve a ticket.
     */
    public function reserve()
    {
        $this->update(['reserved_at' => Carbon::now()]);
    }

    /**
     * Release ticket from an order.
     */
    public function release()
    {
        $this->update(['reserved_at' => null]);
    }
}
