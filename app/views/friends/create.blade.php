@extends("layouts.members")

@section('title')
Friends
@endsection


@section('main-col')
<legend>Add New Friend</legend>

@if($errors->has())
@foreach ($errors->all() as $error)
<div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert"><i class="fa fa-minus-square"></i></button>
  <div>{{ $error }}</div>
</div>      
@endforeach
@endif

{{ Form::open(array('url' => 'members/friends/create', 'method'=>'post')) }}
<div class="row">
    <div class="col-md-10">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" name="first_name" class="form-control" placeholder="Firstname">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" name="last_name" class="form-control" placeholder="Lastname">
                </div> 
            </div>
        </div>
        <div class="form-group">
            <input type="text" name="mobile" class="form-control" id="mobile" placeholder="Mobile">
        </div>
    </div>
    <div class="col-md-2">    
        <button type="submit" class="btn btn-success btn-md btn-block">Add</button>
    </div>
</div>
{{ Form::close() }}
@endsection


