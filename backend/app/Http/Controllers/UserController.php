<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // Registro: crea usuario y genera token
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'telefono' => 'required|string|max:15',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Determinar rol basado en el dominio del correo (@filmgalaxy.com = admin)
        $rol = str_ends_with($request->email, '@filmgalaxy.com') ? 'admin' : 'user';

        $user = User::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'rol'      => $rol,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'       => $user,
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    // Login: valida credenciales y genera token
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $user  = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'       => $user,
            'token'      => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // Logout: elimina el token actual
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'La sesión se ha cerrado correctamente.']);
    }
    // Mostrar todos los usuarios
    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }
    
    // Mostrar usuario por ID
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['user' => $user]);
    }

    // Actualizar usuario (solo el mismo usuario o un admin pueden hacerlo)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->user()->id !== $user->id && $request->user()->rol !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nombre'   => 'string|max:255',
            'apellido' => 'string|max:255',
            'email'    => 'string|email|max:255|unique:users,email,' . $user->id,
            'telefono' => 'string|max:15',
            'password' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->filled('nombre'))   $user->nombre   = $request->nombre;
        if ($request->filled('apellido')) $user->apellido = $request->apellido;
        if ($request->filled('email'))    $user->email    = $request->email;
        if ($request->filled('telefono')) $user->telefono = $request->telefono;
        if ($request->filled('password')) $user->password = Hash::make($request->password);

        $user->save();

        return response()->json(['user' => $user, 'message' => 'Usuario actualizado con éxito']);
    }

    // Eliminar usuario
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() !== $user->id && Auth::user()->rol !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'Usuario eliminado con éxito']);
    }
}
