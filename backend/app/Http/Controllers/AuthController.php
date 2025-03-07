<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Registrar usuario
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'telefono' => 'required|string|max:20',
            'password' => 'required|string|',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'  => 'error', 'message' => 'Error en la validación', 'errors'  => $validator->errors()], 422);
        }

        // Verificar si el correo termina con @filmgalaxy.com para asignar el rol de admin
        $rol = 'user';
        if (str_ends_with($request->email, '@filmgalaxy.com')) {
            $rol = 'admin';
        }

        $user = User::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'rol'      => $rol,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['status'  => 'success', 'message' => 'Usuario registrado exitosamente', 'user'    => $user, 'token'   => $token], 201);
    }

    // Iniciar sesión
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'  => 'error', 'message' => 'Error en la validación', 'errors'  => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user  = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status'  => 'success',
                'message' => 'Login exitoso',
                'user'    => $user,
                'token'   => $token
            ], 200);
        } else {
            return response()->json(['status'  => 'error', 'message' => 'Credenciales incorrectas'], 401);
        }
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['status'  => 'success', 'message' => 'Logout exitoso'], 200);
    }

    // Listar todos los usuarios
    public function index()
    {
        $users = User::all();
        return response()->json(['status'  => 'success', 'message' => 'Listado de usuarios', 'users'   => $users], 200);
    }

    // Obtener un usuario específico
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['status'  => 'error', 'message' => 'Usuario no encontrado'], 404);
        }

        return response()->json(['status'  => 'success', 'message' => 'Usuario encontrado', 'user'    => $user], 200);
    }

    // Actualizar usuario
    public function update(Request $request, $id)
    {
        // Validar los datos
        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $id,
            'telefono' => 'required|string|max:20',
            'password' => 'nullable|string|',
            'rol'      => 'required|string'
        ]);

        // Buscar el usuario
        $user = User::findOrFail($id);

        // Asignar los nuevos valores
        $user->nombre = $data['nombre'];
        $user->apellido = $data['apellido'];
        $user->email = $data['email'];
        $user->telefono = $data['telefono'];
        $user->rol = $data['rol'];

        // Solo actualizar la contraseña si se proporciona
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Usuario actualizado exitosamente',
            'user'    => $user
        ], 200);
    }

    // Eliminar usuario
    public function destroy($id)
    {
        // Buscar y eliminar el usuario
        User::findOrFail($id)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Usuario eliminado exitosamente'
        ], 200);
    }
}
