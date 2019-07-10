<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel VUE Events API</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"/>

    <!-- import CSS -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    {{--        <!-- import JavaScript -->--}}
    {{--        <script src="https://unpkg.com/element-ui/lib/index.js"></script>--}}
</head>
<body>
<div id="app">
    <main-app/>
</div>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>