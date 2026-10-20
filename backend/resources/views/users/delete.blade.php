@extends('Layout.master')

@section('content')
<div class="container mt-4">
    <h1>Eliminar Usuario</h1>
    <p>¿Está seguro que desea eliminar al usuario <strong>{{ $user->nombre }} {{ $user->apellido }}</strong>?</p>
    <form action="{{ route('users.destroy', $user->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Confirmar Eliminación</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
