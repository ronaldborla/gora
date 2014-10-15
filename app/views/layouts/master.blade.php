<!DOCTYPE html>
<html>
<head>
    <title></title>

    <link rel="stylesheet" type="text/css" href="<?php print Asset::url('gora-main-css'); ?>">
    <script src="<?php print Asset::url('gora-main-js'); ?>"></script>

</head>
<body>
    <div class="container">
        @include('layouts/navigation')  
        <div><br><br><br></div>
        <div class="row"> 
            <div class="col-md-8">
                @yield('main-col')
            </div>
            <div class="col-md-4">
                @yield('sidebar')
            </div>
        </div>  
    </div>
</body>
</html>