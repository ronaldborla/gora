@extends("layouts.plain")

@section('title')
Search
@endsection

@section('main-col')
<div class="container">
    <div class="row"> 
        <div class="col-md-offset-3 col-md-6"> 
        </div>
    </div>  
</div>
<div class="intro-header">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <div class="intro-message">
                    <h3>App skemadoo na wiz pa description</h3>
                    <form class="inline">
                        <div class="form-group">
                            <input type="text" class="form-control" id="search" placeholder="Search">
                            <center><div><button type="button" class="btn btn-primary btn-lg">Search</button></div></center>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container -->
</div>
@endsection
