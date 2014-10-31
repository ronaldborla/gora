@extends("layouts.members")

@section('title')
Friends
@endsection


@section('main-col')
<legend>
    <a href="/members/friends/add" class="pull-right"><span class="fa fa-plus"></span></a>
    Friends
</legend>

@if($errors->has())
@foreach ($errors->all() as $error)
<div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert"><i class="fa fa-minus-square"></i></button>
  <div>{{ $error }}</div>
</div>      
@endforeach
@endif

@foreach($friends as $friend)
<?php $user = User::find($friend['friend_id']); ?>
<div class="list-block"> 
    <a href="/members/friends/{{$friend['friend_id']}}" class="pull-right"><span class="glyphicon glyphicon-trash"></span></a>
    <a href="#"><b>{{ $user->first_name }} {{ $user->last_name }}</b></a>        
</div>
@endforeach
@endsection

