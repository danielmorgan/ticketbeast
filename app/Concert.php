<?php

namespace App;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Concert
 *
 * @property int $id
 * @property string $title
 * @property string $subtitle
 * @property \Carbon\Carbon $date
 * @property int $ticket_price
 * @property string $venue
 * @property string $venue_address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $additional_information
 * @property string $published_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read string $formatted_date
 * @property-read string $formatted_start_time
 * @property-read string $ticket_price_in_gbp
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ticket[] $tickets
 * @method static \Illuminate\Database\Query\Builder|\App\Concert published()
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereAdditionalInformation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert wherePublishedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereSubtitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereTicketPrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereVenue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereVenueAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Concert whereZip($value)
 * @mixin \Eloquent
 */
class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'tickets');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

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

    /**
     * Scope the query to only published concerts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * Create an order and some tickets.
     *
     * @param string $email
     * @param int    $ticketQuantity
     * @return \App\Order
     * @throws \App\Exceptions\NotEnoughTicketsException
     * @todo Remove this, only used in tests
     */
    public function orderTickets($email, $ticketQuantity = 1)
    {
        $tickets = $this->findTickets($ticketQuantity);

        return $this->createOrder($email, $tickets);
    }

    /**
     * Get some tickets if available.
     *
     * @param int $quantity
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \App\Exceptions\NotEnoughTicketsException
     */
    public function findTickets($quantity)
    {
        $tickets = $this->tickets()->available()->take($quantity)->get();

        if ($tickets->count() < $quantity) {
            throw new NotEnoughTicketsException;
        }

        return $tickets;
    }

    /**
     * Reserve some tickets for this concert and return a Reservation.
     *
     * @param int    $quantity
     * @param string $email
     * @return \App\Reservation
     */
    public function reserveTickets($quantity, $email)
    {
        $tickets = $this->findTickets($quantity);

        $tickets->each(function (Ticket $ticket) {
            $ticket->reserve();
        });

        return new Reservation($tickets, $email);
    }

    /**
     * Create an order and attach some tickets to it.
     *
     * @param string                                   $email
     * @param \Illuminate\Database\Eloquent\Collection $tickets
     * @return \App\Order
     */
    public function createOrder($email, Collection $tickets)
    {
        return Order::forTickets($tickets, $email, $tickets->sum('price'));
    }

    /**
     * Add empty tickets that can be purchased.
     *
     * @param int $quantity
     * @return $this
     */
    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }

        return $this;
    }

    /**
     * Return number of tickets that remain unpurchased.
     *
     * @return int
     */
    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }

    /**
     * Check if there are any order for a given email.
     *
     * @param string $customerEmail
     * @return bool
     */
    public function hasOrderFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->count() > 0;
    }

    /**
     * Get all orders for a given email.
     *
     * @param string $customerEmail
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function ordersFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->get();
    }
}
