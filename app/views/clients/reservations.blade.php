@extends("layouts.clients")

@section('title')
Estabishment | Vikings Restaurant
@endsection

<style type="text/css">
    tr td {
        font-size: 13px;
    }    
</style>

@section('main-col')
<legend>Zuni Restaurant - Reservations</legend>
<table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Phone</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr class="delay success" style="display:none;">
          <td>6</td>
          <td>Brylle</td>
          <td>09223682399</td>
          <td>Just Now</td>
          <td>New Reservation</td>
        </tr>
        <tr class="danger">
          <td>5</td>
          <td>Abz</td>
          <td>09224364156</td>
          <td>30 mins ago</td>
          <td>Cancelled</td>
        </tr>
        <tr>
          <td>4</td>
          <td>Ronald</td>
          <td>09211336444</td>
          <td>1 day ago</td>
          <td>Reserved</td>
        </tr>
        <tr>
          <td>3</td>
          <td>Karlo</td>
          <td>09112478512</td>
          <td>27 mins ago</td>
          <td>Reserved</td>
        </tr>
        <tr>
          <td>2</td>
          <td>Jopal</td>
          <td>09156874923</td>
          <td>1 hour ago</td>
          <td>Reserved</td>
        </tr>
        <tr>
          <td>2</td>
          <td>Jay Edward</td>
          <td>09222147856</td>
          <td>1 day ago</td>
          <td>Reserved</td>
        </tr>
        <tr>
          <td>1</td>
          <td>Ronald</td>
          <td>09192458746</td>
          <td>2 day ago</td>
          <td>Reserved</td>
        </tr>
      </tbody>
    </table>

@endsection

<script>
    setTimeout(function() {
        $('.delay').show(400);
    }, 1500);
</script>