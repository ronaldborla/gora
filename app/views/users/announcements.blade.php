@extends("layouts.master")

@section('title')
Announcements
@endsection

@section('main-col')
    <h1>Announcements</h1>
    @for($x=0; $x<=5; $x++)
        <div class="announcenment"> 
            <h3>At vero eos et accusamus et iusto odio dignissimos ducimus</h3>
            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>
        </div>
    @endfor
@endsection

@section('sidebar')
<ul class="nav nav-pills nav-stacked">
  <li><a href="#">Sidebar 1</a></li>
  <li><a href="#">Sidebar 2</a></li>
  <li><a href="#">Sidebar 3</a></li>
</ul>
@endsection
