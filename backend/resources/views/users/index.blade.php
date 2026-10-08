@extends('Layout.master')

@section('content')
    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <h1>Lista de Usuarios</h1>
        <a href="{{ route('users.create') }}" class="btn btn-success mb-3">Crear Nuevo Usuario</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->nombre }}</td>
                        <td>{{ $user->apellido }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->telefono }}</td>
                        <td>
                            <!-- Botón para Editar -->
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">Editar</a>

                            <!-- Botón para Eliminar (redirecciona a la confirmación) -->
                            <a href="{{ route('users.delete', $user->id) }}" class="btn btn-danger">Eliminar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection