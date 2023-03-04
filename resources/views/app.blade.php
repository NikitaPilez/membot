<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset('/assets/img/favicon.png') }}">
    <title>Mem</title>
{{--    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />--}}
    <link href="{{ asset('/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
{{--    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>--}}
{{--    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">--}}
    <link id="pagestyle" href="{{ asset('/assets/css/material-dashboard.css?v=3.0.4') }}" rel="stylesheet" />
</head>
<body class="g-sidenav-show  bg-gray-100">
@yield('content')
<script src="{{ asset('/assets/js/core/popper.min.js') }}" ></script>
<script src="{{ asset('/assets/js/core/bootstrap.min.js') }}" ></script>
<script src="{{ asset('/assets/js/plugins/perfect-scrollbar.min.js') }}" ></script>
<script src="{{ asset('/assets/js/plugins/smooth-scrollbar.min.js') }}" ></script>
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>
<script src="{{ asset('/assets/js/material-dashboard.min.js?v=3.0.4') }}"></script>
</body>
</html>

