<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller
{
    /**
     * @var \App\Billing\PaymentGateway
     */
    private $paymentGateway;

    /**
     * ConcertOrdersController constructor.
     *
     * @param \App\Billing\PaymentGateway $paymentGateway
     */
    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($id)
    {
        $concert = Concert::findOrFail($id);
        $ticketQuantity = request('ticket_quantity');
        $amount = $ticketQuantity * $concert->ticket_price;
        $token = request('payment_token');
        $this->paymentGateway->charge($amount, $token);

        $order = $concert->orders()->create([
            'email' => request('email'),
        ]);

        foreach (range(1, $ticketQuantity) as $i) {
            $order->tickets()->create([]);
        }

        return response()->json([], 201);
    }
}
