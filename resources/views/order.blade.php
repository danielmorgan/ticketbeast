@extends('layouts.app')

@section('content')
    <div class="Order">

        <div class="OrderHeader">
            <h1 class="OrderHeader__title">Order Summary</h1>
            <div class="OrderHeader__id">
                <a href="#">#1205372119</a>
            </div>
        </div>

        <div class="OrderSummary">
            <div class="OrderSummary__total">
                <label>Order Total:</label> £110.00
            </div>
            <div class="OrderSummary__card">
                <label>Billed to Card #:</label> **** **** **** 4242
            </div>
        </div>

        <div class="OrderTickets">
            <h2 class="OrderTickets__title">Your Tickets</h2>

            <div class="Ticket">
                <div class="TicketHeader">
                    <div class="TicketHeaderTitles">
                        <h3 class="TicketHeaderTitles__title">This is the title</h3>
                        <h4 class="TicketHeaderTitles__subtitle">and it has a longer subtitle</h4>
                    </div>
                    <div class="TicketHeaderInfo">
                        <div class="TicketHeaderInfo__general-admission">General Admission</div>
                        <div class="TicketHeaderInfo__admit-one">Admit one</div>
                    </div>
                </div>

                <div class="TicketBody">
                    <div class="TicketBodySchedule">
                        @icon('calendar')
                        <div class="TicketBodySchedule__date">Sunday, October 16, 2011</div>
                        <div class="TicketBodySchedule__time">Doors at 8:00PM</div>
                    </div>
                    <div class="TicketBodyLocation">
                        @icon('clock')
                        <div class="TicketBodyLocation__venue">Music Hall of Williamsburg</div>
                        <address class="TicketBodyLocation__address">
                            123 Main St. W<br>
                            Brooklyn, New York 14259
                        </address>
                    </div>
                </div>

                <div class="TicketFooter">
                    <div class="TicketFooter__code">VHIYQWCXA</div>
                    <div class="TicketFooter__email">me@danielmorgan.co.uk</div>
                </div>
            </div>

            <div class="Ticket">
                <div class="TicketHeader">
                    <div class="TicketHeaderTitles">
                        <h3 class="TicketHeaderTitles__title">This is the title</h3>
                        <h4 class="TicketHeaderTitles__subtitle">and it has a longer subtitle</h4>
                    </div>
                    <div class="TicketHeaderInfo">
                        <div class="TicketHeaderInfo__general-admission">General Admission</div>
                        <div class="TicketHeaderInfo__admit-one">Admit one</div>
                    </div>
                </div>

                <div class="TicketBody">
                    <div class="TicketBodySchedule">
                        @icon('calendar')
                        <div class="TicketBodySchedule__date">Sunday, October 16, 2011</div>
                        <div class="TicketBodySchedule__time">Doors at 8:00PM</div>
                    </div>
                    <div class="TicketBodyLocation">
                        @icon('clock')
                        <div class="TicketBodyLocation__venue">Music Hall of Williamsburg</div>
                        <address class="TicketBodyLocation__address">
                            123 Main St. W<br>
                            Brooklyn, New York 14259
                        </address>
                    </div>
                </div>

                <div class="TicketFooter">
                    <div class="TicketFooter__code">VHIYQWCXA</div>
                    <div class="TicketFooter__email">me@danielmorgan.co.uk</div>
                </div>
            </div>
        </div>

    </div>
@endsection
