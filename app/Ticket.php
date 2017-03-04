<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
        $this->update(['order_id' => null]);
    }
}
