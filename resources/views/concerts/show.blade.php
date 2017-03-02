@extends('layouts.app')

@section('content')
    <div class="Card">
        <div class="Card__content Concert">
            <h1 class="Concert__title">{{ $concert->title }}</h1>
            <h2 class="Concert__subtitle">{{ $concert->subtitle }}</h2>

            <div class="Concert__date">
                @icon('calendar')
                {{ $concert->formatted_date }}
            </div>

            <div class="Concert__time">
                @icon('clock')
                Doors at {{ $concert->formatted_start_time }}
            </div>

            <div class="Concert__ticket-price">
                @icon('coin-pound')
                &pound;{{ $concert->ticket_price_in_gbp }}
            </div>

            <div class="Concert__venue">
                @icon('location')
                {{ $concert->venue }}
                <div class="Concert__indented">
                    {{ $concert->venue_address }}<br>
                    {{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}
                </div>
            </div>

            <div class="Concert__additional-information">
                @icon('info')
                Additional Information
                <div class="Concert__indented">
                    {{ $concert->additional_information }}
                </div>
            </div>
        </div>

        <div class="Card__content">
            {{--Vue component--}}
            <Checkout ticket_price="{{ $concert->ticket_price }}"></Checkout>
        </div>
    </div>
@endsection
