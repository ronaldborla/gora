<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    <link rel="stylesheet" type="text/css" href="<?php print Asset::url('gora-main-css'); ?>">
    <script src="<?php print Asset::url('gora-main-js'); ?>"></script>
</head>
<body>
        @include('layouts/navigation')  
        @yield('main-col') 
</body>
</html>