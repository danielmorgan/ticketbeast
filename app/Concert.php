<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

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
}
