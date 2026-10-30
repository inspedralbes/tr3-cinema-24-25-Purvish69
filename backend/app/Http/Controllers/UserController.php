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
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            $authUser = $request->user();
            if (!$authUser || $authUser->rol !== 'admin') {
                return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador.'], 403);
            }
            return response()->json(['users' => User::all()]);
        }
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
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = new User([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
        ]);
        
        $authUser = $request->user();
        if ($request->has('rol') && $authUser && $authUser->rol === 'admin') {
            $user->rol = in_array($request->rol, ['admin', 'user']) ? $request->rol : 'user';
        } else {
            $user->rol = 'user';
        }
        
        $user->save();

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
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = new User([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
        ]);
        $user->rol = 'user';
        $user->save();

        try {
            Mail::to($user->email)->send(new SendMail($user));
        } catch (\Throwable $e) {
            // No bloquear el registro si el servicio de correo no está disponible en desarrollo
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'       => $user,
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    // login para obtener token y si es una petición de front envía en JSON si no hace redirect
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                if ($request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Las credenciales proporcionadas son incorrectas.'
                    ], 401);
                }
                return redirect()->back()->withErrors(['message' => 'Las credenciales proporcionadas son incorrectas.']);
            }

            $user = User::where('email', $request->email)->firstOrFail();

            // Si es petición web (panel de administración), rechazar usuarios no admin
            if (!$request->wantsJson() && !$request->is('api/*')) {
                if ($user->rol !== 'admin') {
                    Auth::logout();
                    if ($request->hasSession()) {
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                    }
                    return redirect()->back()->withErrors(['message' => 'Acceso denegado. Se requieren permisos de administrador.']);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'     => 'success',
                    'user'       => $user,
                    'token'      => $token,
                    'token_type' => 'Bearer',
                ]);
            }

            return redirect()->route('users.index')->with('success', 'Has iniciado sesión correctamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Error de autenticación.'
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

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Sesión cerrada correctamente']);
        }

        return redirect('/login');
    }

    // Mostrar usuario por ID (para API) con validación de autorización
    public function show(Request $request, $id)
    {
        $authUser = $request->user();
        if (!$authUser || ($authUser->id != $id && $authUser->rol !== 'admin')) {
            return response()->json(['message' => 'Acceso no autorizado.'], 403);
        }

        $user = User::findOrFail($id);
        return response()->json(['user' => $user]);
    }

    // Actualizar usuario (solo el mismo usuario o un admin pueden hacerlo)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $authUser = $request->user();

        if (!$authUser || ($authUser->id != $user->id && $authUser->rol !== 'admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nombre'   => 'string|max:255',
            'apellido' => 'string|max:255',
            'email'    => 'string|email|max:255|unique:users,email,' . $user->id,
            'telefono' => 'string|max:15',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->filled('nombre'))   $user->nombre   = $request->nombre;
        if ($request->filled('apellido')) $user->apellido = $request->apellido;
        if ($request->filled('email'))    $user->email    = $request->email;
        if ($request->filled('telefono')) $user->telefono = $request->telefono;
        if ($request->filled('password')) $user->password = Hash::make($request->password);

        // Solo un administrador puede modificar el rol
        if ($request->has('rol') && $authUser && $authUser->rol === 'admin') {
            $user->rol = $request->rol;
        }

        $user->save();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['user' => $user, 'message' => 'Usuario actualizado con éxito']);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado con éxito.');
    }

    // Eliminar usuario
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $authUser = $request->user();

        if (!$authUser || ($authUser->id != $user->id && $authUser->rol !== 'admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Usuario eliminado con éxito']);
        }

        return redirect()->route('users.index')->with('success', 'Usuario eliminado con éxito.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
}
