@extends("layouts.centered")

@section('title')
Login
@endsection

@section('main-col')
<div class="row">
  <div class="col-md-offset-4 col-md-4">
    <h2>Login</h2>
    <hr>
    @if($errors->has())
    @foreach ($errors->all() as $error)
    <div class="alert alert-danger alert-block">
      <button type="button" class="close" data-dismiss="alert"><i class="fa fa-minus-square"></i></button>
      <div>{{ $error }}</div>
    </div>      
    @endforeach
    @endif
    {{ Form::open(array('url' => '/authenticate', 'method'=>'post')) }}
    <div class="form-group">
      <label for="mobile">Mobile</label>
      <input type="text" name="mobile" class="form-control" id="mobile" placeholder="Enter mobile">
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" name="password" class="form-control" id="password" placeholder="Password">
    </div>
    <button type="submit" class="btn btn-success btn-lg">Login</button>    
    {{ Form::close() }}
  </div>
</div>
@endsection
