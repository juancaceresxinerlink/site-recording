@extends('layouts.app')


@section('content')



@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<center>
<h2>Administrador de roles</h2>
</center>
<div class ="table-center">
<table class = "table table-bordered">
  <tr>
     <th>No</th>
     <th>Name</th>
     <th width="280px">Action</th>
  </tr>
    @foreach ($roles as $key => $role)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $role->name }}</td>
        <td>
            <a class="btn btn-info" href="{{ route('roles.show',$role->id) }}">Mostrar</a>
            @can('admin-roles')
                <a class="btn btn-primary" href="{{ route('roles.edit',$role->id) }}">Editar</a>
            @endcan
            @can('admin-roles')
                {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                {!! Form::close() !!}
            @endcan
        </td>
    </tr>
    @endforeach
    </div>
</table>


<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-right">
        @can('admin-roles')
            <a class="btn btn-secondary btn-sm" href="{{ route('roles.create') }}"> Crear un nuevo rol</a>
            @endcan
        </div>
    </div>
</div>


{!! $roles->render() !!}


@endsection