@extends('layouts.app')


@section('content')



@if (count($errors) > 0)
  <div class="alert alert-danger">
    <strong>Error!</strong> Favor validar los siguientes errores<br><br>
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
                 <div class="card-header">Actualizar usuario</div>
                 <div class="card-body">
             
                 {!! Form::model($user, ['method' => 'PATCH','route' => ['users.update', $user->id]]) !!}
            
                 <strong>Name:</strong>
                 {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}

                 <strong>Email:</strong>
                {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}

                <strong>Password:</strong>
                {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}


                <strong>Confirm Password:</strong>
                {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}

                <strong>Role:</strong>
                {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-control','multiple')) !!}

                <strong>Colas:</strong>
                {!! Form::select('queues[]',$queues,$Userqueues, array('class' => 'form-control','multiple')) !!}
             


                <br>
                <br>
                <button type="submit" class="btn btn-primary">Actualizar</button>
                {!! Form::close() !!}
                 </div>
             </div>
         </div>
     </div>
 </div>











@endsection