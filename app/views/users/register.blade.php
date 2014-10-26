@extends("layouts.centered")

@section('title')
Register
@endsection

@section('main-col')
<div class="row">
  <div class="col-md-offset-4 col-md-4">
    <h2><center>Register</center></h2>
    <hr>
    @if($errors->has())
    @foreach ($errors->all() as $error)
    <div class="alert alert-danger alert-block">
      <button type="button" class="close" data-dismiss="alert"><i class="fa fa-minus-square"></i></button>
      <div>{{ $error }}</div>
    </div>      
    @endforeach
    @endif
    {{ Form::open(array('url' => '/register-user', 'method'=>'post')) }}
    <label for="mobile">Name</label>
    <div class="form-group">
      <div class="row">
        <div class="col-md-6">
          <input type="text" name="first_name" class="form-control" id="first_name" placeholder="First">
        </div>
        <div class="col-md-6">
          <input type="text" name="last_name" class="form-control" id="last_name" placeholder="Last">
        </div>
      </div>
    </div>
    <div class="form-group">
      <label for="mobile">Mobile</label>
      <input type="text" name="mobile" class="form-control" id="mobile" placeholder="Enter mobile">
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" name="password" class="form-control" id="password" placeholder="Password">
    </div>
    <center><button type="submit" class="btn btn-success btn-lg">Register</button></center>   
    {{ Form::close() }}
  </div>
</div>
@endsection
