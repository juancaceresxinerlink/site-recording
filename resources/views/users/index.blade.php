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

<div class ="table-center">
<table class="table table-bordered">
 <tr>
   <th>No</th>
   <th>Nombre</th>
   <th>Email</th>
   <th>Roles</th>
   <th width="280px">Acciones</th>
 </tr>
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
 </div> 
</table>


<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-right">
            <a class="btn btn-secondary btn-sm" href="{{ route('users.create') }}"> Crear nuevo usuario</a>
        </div>
    </div>
</div>


{!! $data->render() !!}



@endsection