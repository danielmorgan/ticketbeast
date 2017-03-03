<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Only return tickets that haven't been purchased.
     *
     * @param $query
     * @return mixed
     */
    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id');
    }
}
