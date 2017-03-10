<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="{{ elixir('css/app.css') }}">

    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
            'stripeKey' => config('services.stripe.key'),
        ]) !!}
    </script>

    <title>TicketBeast</title>
</head>
<body>
    <div id="app">
        @yield('content')
    </div>

    {{--<script src="https://checkout.stripe.com/checkout.js"></script>--}}
    <script type="text/javascript" src="{{ elixir('js/app.js') }}"></script>

    {{--<link href="https://fonts.googleapis.com/css?family=Karla:400,700|Nunito:600" rel="stylesheet">--}}
</body>
</html>
