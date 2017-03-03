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
     * Release all tickets and delete the order.
     */
    public function cancel()
    {
        $this->tickets->each(function ($ticket) {
            $ticket->release();
        });

        $this->delete();
    }
}
