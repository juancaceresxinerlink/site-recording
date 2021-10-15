@extends('layouts.app')


@section('content')



@if (count($errors) > 0)
  <div class="alert alert-danger">
    <strong>Error</strong> Favor validar lo siguiente<br><br>
    <ul>
       @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
       @endforeach
    </ul>
  </div>
@endif




<div class="container">
     <div class="row justify-content-center">
         <div class="col-md-8">
             <div class="card">
                 <div class="card-header">Crear usuario</div>
                 <div class="card-body">
             
             {!! Form::open(array('route' => 'users.store','method'=>'POST')) !!}
             
             <strong>Nombre:</strong>
             {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
            
             <strong>Email:</strong>
             {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}

             <strong>Password:</strong>
             {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}


             <strong>Confirmar Password:</strong>
             {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}

                
             <strong>Roles:</strong>
             {!! Form::select('roles[]', $roles,[], array('class' => 'form-control','multiple')) !!}

             <strong>Colas:</strong>
             {!! Form::select('queues[]', $queues,[], array('class' => 'form-control','multiple')) !!}
             

             <br>
             <br>
             <button type="submit" class="btn btn-primary">Guardar</button>
             

             {!! Form::close() !!}
            
            
 
                 </div>
             </div>
         </div>
     </div>
 </div>




@endsection