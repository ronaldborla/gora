@extends("layouts.clients")

@section('title')
Friends
@endsection

@section('main-col')
    <legend>Friends</legend>

    @for($x=0; $x<=5; $x++)
    <div class="list-block"> 
        <span class="pull-right">Group</span>
        <a href="#"><b>Friend <?php print $x ?></b></a>
    </div>
    @endfor
@endsection