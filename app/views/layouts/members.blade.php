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
        <div class="row"> 
            <div class="col-md-offset-2 col-md-2">    
                <div class="sidebar">      
                    <a href="<?php print url('members/profile'); ?>">
                        <h3>Jon Snow</h3>
                    </a>
                    <ul class="nav nav-pills nav-stacked">
                      <li>
                          <a href="<?php print url('members/subscriptions'); ?>">Subscriptions</a>
                      </li>
                      <li>
                          <a href="<?php print url('members/friends'); ?>">Friends</a>
                      </li>
                      <li>
                          <a href="<?php print url('search'); ?>">Search</a>
                      </li>
                  </ul>
              </div>      
            </div>
            <div class="col-md-5">
                @yield('main-col')
            </div>
        </div>  
    </div>
</body>
</html>