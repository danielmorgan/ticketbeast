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
    public function store(Request $request, $id)
    {
        $this->validate(request(), [
            'email' => 'required',
        ]);

        /** @var Concert $concert */
        $concert = Concert::findOrFail($id);

        $this->paymentGateway->charge(
            $request->ticket_quantity * $concert->ticket_price,
            $request->payment_token
        );

        $order = $concert->orderTickets(
            $request->email,
            $request->ticket_quantity
        );

        return response()->json($order, 201);
    }
}
