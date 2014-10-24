@extends("layouts.members")

@section('title')
Subscriptions
@endsection

@section('main-col')
    <legend>Subscriptions</legend>

    @for($x=0; $x<=5; $x++)
    <div class="list-block"> 
        <p><b><a href="#">Establisment <?php print $x ?></a></b> Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
        <p><small><?php print date('F j, Y, g:i a') ?></small></p>
    </div>
    @endfor
@endsection