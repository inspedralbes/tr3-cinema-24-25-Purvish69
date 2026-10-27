<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // Mostrar pagos (solo del usuario autenticado a menos que sea admin)
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $query = Payment::with(['user', 'ticket.movieSession.movie']);

        if ($user->rol !== 'admin') {
            $query->where('user_id', $user->id);
        } elseif ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $payments = $query->latest('created_at')->get();
        return response()->json($payments);
    }

    // Crear un pago seguro
    public function store(Request $request)
    {
        $user = $request->user();
        $userId = $user ? $user->id : $request->input('user_id');

        $validator = Validator::make($request->all(), [
            'amount'         => 'required|numeric|min:0.50|max:1000',
            'payment_method' => 'required|string|in:card,paypal,bizum,cash',
            'status'         => 'required|in:completed,pending,failed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al crear el pago',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Si no hay usuario autenticado y el ID proporcionado no existe en BD
        if (!$userId || !\App\Models\User::where('id', $userId)->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Usuario no válido para el pago'
            ], 422);
        }

        $payment = Payment::create([
            'user_id'        => $userId,
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'status'         => $request->status,
            'transaction_id' => (string) Str::uuid(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pago creado exitosamente',
            'data'    => $payment
        ], 201);
    }

    // Mostrar un pago asegurando autorización
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $payment = Payment::with(['user', 'ticket.movieSession.movie'])->findOrFail($id);

        if ($payment->user_id !== $user->id && $user->rol !== 'admin') {
            return response()->json(['message' => 'Acceso no autorizado al pago'], 403);
        }

        return response()->json($payment);
    }

    // Actualizar un pago (solo admin)
    public function update(Request $request, Payment $payment)
    {
        $user = $request->user();
        if (!$user || $user->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'amount'         => 'sometimes|numeric|min:0.50|max:1000',
            'payment_method' => 'sometimes|string|in:card,paypal,bizum,cash',
            'status'         => 'sometimes|in:completed,pending,failed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment->update($request->only(['amount', 'payment_method', 'status']));
        return response()->json($payment);
    }

    // Eliminar un pago (solo admin)
    public function destroy(Request $request, Payment $payment)
    {
        $user = $request->user();
        if (!$user || $user->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        $payment->delete();
        return response()->json(['message' => 'Pago eliminado correctamente']);
    }
}
