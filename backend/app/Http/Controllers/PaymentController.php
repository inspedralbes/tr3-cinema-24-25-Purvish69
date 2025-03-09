<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'ticket_id' => 'required|exists:tickets,id',
            'metodo_pago' => 'required|string',
            'estado' => 'required|in:pagado,pendiente,rechazado',
            'importe_total' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'  => 'error', 'message' => 'Error al crear el pago', 'errors'  => $validator->errors()], 422);
        }

        // Verificar que el ticket pertenezca al usuario
        $ticket = \App\Models\Ticket::findOrFail($request->ticket_id);

        if ($ticket->user_id != $request->user_id) {
            return response()->json([
                'status' => 'error',
                'error' => 'El ticket no pertenece al usuario indicado'
            ], 422);
        }
        // Verificar que el ticket no tenga ya un pago
        $existingPayment = Payment::where('ticket_id', $request->ticket_id)->first();

        if ($existingPayment) {
            return response()->json([
                'status' => 'error',
                'error' => 'Ya existe un pago para este ticket'
            ], 422);
        }

        $payment = Payment::create($request->all());
        return response()->json($payment, 201);
    }

    // mostrar un pago
    public function show(Payment $payment)
    {
        $payment->load(['user', 'ticket.movieSession.movie']);
        return response()->json($payment);
    }

    public function update(Request $request,$id)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|required|exists:users,id',
            'ticket_id' => 'sometimes|required|exists:tickets,id',
            'metodo_pago' => 'sometimes|required|string',
            'estado' => 'sometimes|required|in:pagado,pendiente,rechazado',
            'importe_total' => 'sometimes|required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'  => 'error', 'message' => 'Error al actualizar el pago', 'errors'  => $validator->errors()], 422);
        }

        if (($request->has('ticket_id') && $request->ticket_id != $id->ticket_id) ||
            ($request->has('user_id') && $request->user_id != $id->user_id)
        ) {

            // Verificar que el ticket pertenezca al usuario
            $ticketId = $request->ticket_id ?? $id->ticket_id;
            $userId = $request->user_id ?? $id->user_id;

            $ticket = \App\Models\Ticket::findOrFail($ticketId);

            if ($ticket->user_id != $userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El ticket no pertenece al usuario indicado'
                ], 422);
            }
        }

        $id->update($request->all());
        return response()->json($id);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->json(null, 204);
    }
}
