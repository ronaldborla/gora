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
                          <a href="<?php print url('members/subscriptions'); ?>">
                          <span class="fa fa-building"></span>
                          Subscriptions
                          </a>
                      </li>
                      <li>
                          <a href="<?php print url('members/friends'); ?>">
                          <span class="fa fa-users"></span>
                          Friends
                          </a>
                      </li>
                      <li>
                          <a href="<?php print url('search'); ?>">
                          <span class="fa fa-search"></span>
                          Search
                          </a>
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