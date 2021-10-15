@extends('layouts.app')


@section('content')

<center>
<h2>Administrador de usuarios</h2>
</center>



@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif


<div class="col-md-6">
  
       <input type="text" id="search" class="form-control" placeholder="Buscar usuario" value="">
   
      <!-- <input type="text" id="search" placeholder="Type to search"> -->

       <!--
      </div>
      <br>
      <div class="col-md-2">
       @csrf
       <button type="button" name="search" id="search" class="btn btn-success">Search</button>
      </div>
-->
</div>
<br>

<div id="original-table" class ="table-center">
<table class="table table-bordered">
 <tr>
   <th>No</th>
   <th>Nombre</th>
   <th>Email original</th>
   <th>Roles</th>
   <th width="280px">Acciones</th>
 </tr>
 <tbody>
 @foreach ($data as $key => $user)
  <tr>
    <td>{{ ++$i }}</td>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td>
      @if(!empty($user->getRoleNames()))
        @foreach($user->getRoleNames() as $v)
           <label class="badge badge-success">{{ $v }}</label>
        @endforeach
      @endif
    </td>
    <td>
       
       <a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}">Editar</a>
        {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}
    </td>
  </tr>
 @endforeach
</tbody>
 </div> 
</table>

{{-- Pagination --}}
<div class="d-flex justify-content-center">
            {!! $data->links() !!}
</div>

<!--
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-right">
            <a class="btn btn-secondary btn-sm" href="{{ route('users.create') }}"> Crear nuevo usuario</a>
        </div>
    </div>
</div>
-->
</div>



<script type="text/javascript">


var $rows = $('#table tr');
$('#search').keyup(function() {

   console.log("Cambiando");
   var val = $.trim($(this).val()).replace(/ +/g, ' ');

   
   var _token = $("input[name=_token]").val();

   console.log(_token);
   console.log(val);

   $.ajax({
   url:"{{ route('users.search') }}",
   method:"POST",
   data:{full_text_search_query:val, _token:_token},
   dataType:"json",   
   success:function(data)
   {
    console.log(data);
    $("#original-table").html(data);
    


   }
});
});
</script>




@endsection