<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    <link rel="stylesheet" type="text/css" href="<?php print Asset::url('gora-main-css'); ?>">
    <script src="<?php print Asset::url('gora-main-js'); ?>"></script>
</head>
<body>
    <div class="container">
        @include('layouts/navigation')  
        @yield('main-col') 
    </div>
</body>
</html>