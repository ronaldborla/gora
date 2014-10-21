@extends("layouts.plain")

@section('title')
Search
@endsection

@section('main-col')
<div class="container">
    <div class="row"> 
        <div class="col-md-offset-3 col-md-6"> 
            <form class="inline">
                <div class="form-group">
                    <input type="text" class="form-control" id="search" placeholder="Search">
                    <center><div><button type="button" class="btn btn-primary btn-lg">Search</button></div></center>
                </div>
            </form>
        </div>
    </div>  
</div>
@endsection
