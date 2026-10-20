<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // Para mostrar todos los usuarios en la página principal
    public function index()
    {
        $users = User::all();
        return view('users.index', ['users' => $users]);
    }

    // Para mostrar el formulario de creación de usuario
    public function create()
    {
        return view('users.create');
    }

    // Para guardar un nuevo usuario
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'telefono' => 'required|string|max:15',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Determinar rol basado en el dominio del correo (@filmgalaxy.com = admin)
        $rol = str_ends_with($request->email, '@filmgalaxy.com') ? 'admin' : 'user';

        User::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'rol'      => $rol,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado con éxito.');
    }

    // Para mostrar el formulario de edición
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', ['user' => $user]);
    }

    // Para confirmar antes de eliminar
    public function confirmDelete($id)
    {
        $user = User::findOrFail($id);
        return view('users.delete', ['user' => $user]);
    }

    // Registro: crea usuario y genera token (para API)
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

        Mail::to($user->email)->send(new SendMail($user));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'       => $user,
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    // login para obtener token y si es una apetecion de front envia en JSON si no hace return redirect para devolver 
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Las credenciales proporcionadas son incorrectas.'
                    ], 401);
                }
                return redirect()->back()->withErrors(['message' => 'Las credenciales proporcionadas son incorrectas.']);
            }

            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            if ($request->wantsJson()) {
                return response()->json([
                    'status'     => 'success',
                    'user'       => $user,
                    'token'      => $token,
                    'token_type' => 'Bearer',
                ]);
            }

            // Redirigir a una ruta definida para los usuarios autenticados
            return redirect()->route('users.index')->with('success', 'Has iniciado sesión correctamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }


    public function logout(Request $request)
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // Mostrar usuario por ID (para API)
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['user' => $user]);
    }

    // Actualizar usuario (solo el mismo usuario o un admin pueden hacerlo)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Para la web no necesitamos esta validación ya que usamos middleware
        if ($request->wantsJson() && $request->user()->id !== $user->id && $request->user()->rol !== 'admin') {
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
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->filled('nombre'))   $user->nombre   = $request->nombre;
        if ($request->filled('apellido')) $user->apellido = $request->apellido;
        if ($request->filled('email'))    $user->email    = $request->email;
        if ($request->filled('telefono')) $user->telefono = $request->telefono;
        if ($request->filled('password')) $user->password = Hash::make($request->password);

        $user->save();

        if ($request->wantsJson()) {
            return response()->json(['user' => $user, 'message' => 'Usuario actualizado con éxito']);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado con éxito.');
    }

    // Eliminar usuario
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->wantsJson() && $request->user()->id !== $user->id && $request->user()->rol !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Usuario eliminado con éxito']);
        }

        return redirect()->route('users.index')->with('success', 'Usuario eliminado con éxito.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
}
