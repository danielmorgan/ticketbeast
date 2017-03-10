@extends('layouts.app')

@section('content')
    <div class="Concert">
        <div class="ConcertHeader">
            <h1 class="ConcertHeader__title">{{ $concert->title }}</h1>
            <h2 class="ConcertHeader__subtitle">{{ $concert->subtitle }}</h2>
        </div>

        <div class="ConcertDetails">
            <div class="ConcertDetails__date">
                @icon('calendar')
                {{ $concert->formatted_date }}
            </div>

            <div class="ConcertDetails__time">
                @icon('clock')
                Doors at {{ $concert->formatted_start_time }}
            </div>

            <div class="ConcertDetails__ticket-price">
                @icon('coin-pound')
                £{{ $concert->ticket_price_in_gbp }}
            </div>

            <div class="ConcertDetails__venue">
                @icon('location')
                {{ $concert->venue }}
                <div class="ConcertDetails__indented">
                    {{ $concert->venue_address }}<br>
                    {{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}
                </div>
            </div>

            <div class="ConcertDetails__additional-information">
                @icon('info')
                Additional Information
                <div class="ConcertDetails__indented">
                    {{ $concert->additional_information }}
                </div>
            </div>
        </div>

        {{--Vue component--}}
        <Checkout root_class="ConcertCheckout"
                  concert_id="{{ $concert->id }}"
                  ticket_price="{{ $concert->ticket_price }}"
                  tickets_remaining="{{ $concert->ticketsRemaining() }}">
        </Checkout>
    </div>
@endsection

@push('scripts')
    <script src="https://checkout.stripe.com/checkout.js"></script>
    <script type="text/javascript" src="{{ elixir('js/app.js') }}"></script>
@endpush
