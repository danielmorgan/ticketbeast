<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Exceptions\PaymentFailedException;
use App\Order;
use App\Reservation;
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
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $id)
    {
        /** @var Concert $concert */
        $concert = Concert::published()->findOrFail($id);

        $this->validate($request, [
            'email'           => ['required', 'email'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
            'payment_token'   => ['required'],
        ]);

        try {
            $tickets = $concert->findTickets($request->ticket_quantity);
            $reservation = new Reservation($tickets);

            $this->paymentGateway->charge(
                $reservation->totalCost(),
                $request->payment_token
            );

            $order = Order::forTickets(
                $tickets,
                $request->email,
                $reservation->totalCost()
            );

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            return response()->json(null, 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json(null, 422);
        }
    }
}
