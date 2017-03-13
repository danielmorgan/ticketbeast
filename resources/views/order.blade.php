@extends('layouts.app')

@section('content')
    <div class="Order">

        <div class="OrderHeader">
            <h1 class="OrderHeader__title">Order Summary</h1>
            <div class="OrderHeader__id">
                <a href="{{ url("/orders/{$order->confirmation_number}") }}">
                    {{ $order->confirmation_number }}
                </a>
            </div>
        </div>

        <div class="OrderSummary">
            <div class="OrderSummary__total">
                <label>Order Total:</label> £{{ number_format($order->amount / 100, 2) }}
            </div>
            <div class="OrderSummary__card">
                <label>Billed to Card #:</label>
                **** **** **** {{ $order->card_last_four }}
            </div>
        </div>

        <div class="OrderTickets">
            <h2 class="OrderTickets__title">Your Tickets</h2>

            @foreach ($order->tickets as $ticket)
                <div class="Ticket">
                    <div class="TicketHeader">
                        <div class="TicketHeaderTitles">
                            <h3 class="TicketHeaderTitles__title">{{ $ticket->concert->title }}</h3>
                            <div class="TicketHeaderTitles__subtitle">{{ $ticket->concert->subtitle }}</div>
                        </div>
                        <div class="TicketHeaderInfo">
                            <div class="TicketHeaderInfo__general-admission">General Admission</div>
                            <div class="TicketHeaderInfo__admit-one">Admit one</div>
                        </div>
                    </div>

                    <div class="TicketBody">
                        <div class="TicketBodySchedule">
                            <div class="TicketBodySchedule__date">
                                @icon('calendar')
                                <time datetime="{{ $ticket->concert->date->format('Y-m-d H:i') }}">
                                    {{ $ticket->concert->date->format('l, F jS, Y') }}
                                </time>
                            </div>
                            <div class="TicketBodySchedule__time">
                                Doors at {{ $ticket->concert->date->format('g:ia') }}
                            </div>
                        </div>
                        <div class="TicketBodyLocation">
                            <div class="TicketBodyLocation__venue">
                                @icon('clock')
                                {{ $ticket->concert->venue }}
                            </div>
                            <address class="TicketBodyLocation__address">
                                <p>{{ $ticket->concert->venue_address }}</p>
                                <p>{{ $ticket->concert->city }}, {{ $ticket->concert->state }} {{ $ticket->concert->zip }}</p>
                            </address>
                        </div>
                    </div>

                    <div class="TicketFooter">
                        <div class="TicketFooter__code">{{ $ticket->code }}</div>
                        <div class="TicketFooter__email">{{ $order->email }}</div>
                    </div>
                </div>
            @endforeach

        </div>

    </div>
@endsection
