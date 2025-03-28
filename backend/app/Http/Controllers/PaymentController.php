<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // mostrar todos los pagos
    public function index()
    {
        $query = Payment::with(['user', 'ticket.movieSession.movie']);

        // filtrar pago por user_id si se proporciona
        if (request()->has('user_id')) {
            $query->where('user_id', request('user_id'));
        }
        $payments = $query->get();
        return response()->json($payments);
    }

    // crear un pago
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'status' => 'required|in:completed,pending,failed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el pago',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generar un ID de transacción único
        $paymentData = $request->all();
        $paymentData['transaction_id'] = Str::uuid();

        $payment = Payment::create($paymentData);

        return response()->json([
            'status' => 'success',
            'message' => 'Pago creado exitosamente',
            'data' => $payment
        ], 201);
    }

    // mostrar un pago
    public function show(Payment $payment)
    {
        $payment->load(['user', 'ticket.movieSession.movie']);
        return response()->json($payment);
    }

    public function update(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'ticket_id' => 'nullable|exists:tickets,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'status' => 'required|in:completed,pending,failed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el pago',
                'errors' => $validator->errors()
            ], 422);
        }

        $payment->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Pago actualizado exitosamente',
            'data' => $payment
        ]);
    }

    // Eliminar un pago
    public function destroy(Payment $payment)
    {
        // Verificar si hay tickets asociados
        if ($payment->tickets()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se puede eliminar el pago porque tiene tickets asociados'
            ], 422);
        }

        $payment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pago eliminado exitosamente'
        ], 200);
    }
}
