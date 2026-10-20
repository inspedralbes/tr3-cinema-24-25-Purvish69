<?php

namespace App\Http\Controllers;

use App\Models\MovieSession;
use App\Models\Seat;
use App\Models\SeatHold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SeatHoldController extends Controller
{
    /**
     * Parsea un identificador de asiento como "A-1" o "A1" en fila y número.
     */
    private function parseSeatCode(string $code): array
    {
        if (preg_match('/^([A-Za-z]+)-?(\d+)$/', trim($code), $matches)) {
            return [strtoupper($matches[1]), (int) $matches[2]];
        }
        return [strtoupper(trim($code)), null];
    }

    /**
     * POST /holds
     * Recibe funcion_id, asientos[], hold_token.
     * Dentro de una transacción:
     * - Borra holds caducados de esa función.
     * - Comprueba que los asientos no estén comprados ni retenidos por otro token.
     * - Si alguno está ocupado/retenido por otro, devuelve 409 con los asientos en conflicto y no crea ninguno.
     * - Crea/renueva los holds (5 minutos).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'funcion_id' => 'required|exists:movieSessions,id',
            'asientos'   => 'required|array|min:1',
            'asientos.*' => 'required|string',
            'hold_token' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $funcionId = $request->funcion_id;
        $asientos = array_unique(array_map('trim', (array) $request->asientos));
        $holdToken = $request->hold_token;

        return DB::transaction(function () use ($funcionId, $asientos, $holdToken, $request) {
            // 1. Borrar holds caducados de esa función
            SeatHold::where('funcion_id', $funcionId)
                ->where('expires_at', '<=', now())
                ->delete();

            $conflicts = [];

            foreach ($asientos as $asiento) {
                [$fila, $numero] = $this->parseSeatCode($asiento);

                // Comprobar si ya está comprado
                $isBought = false;
                if ($fila && $numero !== null) {
                    $isBought = Seat::where('movieSession_id', $funcionId)
                        ->where('fila', $fila)
                        ->where('numero', $numero)
                        ->where('estado', 'ocupada')
                        ->exists();
                }

                if ($isBought) {
                    $conflicts[] = $asiento;
                    continue;
                }

                // Comprobar si está retenido por otro token activo
                $isHeldByOther = SeatHold::where('funcion_id', $funcionId)
                    ->where('asiento', $asiento)
                    ->where('hold_token', '!=', $holdToken)
                    ->where('expires_at', '>', now())
                    ->exists();

                if ($isHeldByOther) {
                    $conflicts[] = $asiento;
                }
            }

            // Si alguno está ocupado, devuelve 409 con los asientos en conflicto y no crea ninguno
            if (!empty($conflicts)) {
                return response()->json([
                    'message'   => 'Este asiento está en proceso de reserva por otro usuario',
                    'conflicts' => array_values(array_unique($conflicts))
                ], 409);
            }

            // Crear o renovar los holds (5 min)
            $expiresAt = now()->addMinutes(5);
            $userId = auth('sanctum')->id() ?? $request->user()?->id;

            foreach ($asientos as $asiento) {
                SeatHold::updateOrCreate(
                    [
                        'funcion_id' => $funcionId,
                        'asiento'    => $asiento,
                    ],
                    [
                        'user_id'    => $userId,
                        'hold_token' => $holdToken,
                        'expires_at' => $expiresAt,
                    ]
                );
            }

            return response()->json([
                'status'     => 'success',
                'message'    => 'Asientos retenidos correctamente',
                'hold_token' => $holdToken,
                'asientos'   => array_values($asientos),
                'expires_at' => $expiresAt->toIso8601String(),
            ], 200);
        });
    }

    /**
     * DELETE /holds
     * Libera los asientos indicados del token.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hold_token' => 'required|string',
            'funcion_id' => 'sometimes|exists:movieSessions,id',
            'asientos'   => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = SeatHold::where('hold_token', $request->hold_token);

        if ($request->filled('funcion_id')) {
            $query->where('funcion_id', $request->funcion_id);
        }

        if ($request->filled('asientos')) {
            $asientos = (array) $request->asientos;
            $query->whereIn('asiento', $asientos);
        }

        $deleted = $query->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Asientos liberados correctamente',
            'deleted' => $deleted
        ], 200);
    }

    /**
     * GET /funciones/{id}/asientos
     * Devuelve asientos comprados y asientos retenidos por otros (excluye los del propio token)
     * ignorando holds caducados.
     */
    public function getAsientos(Request $request, $id)
    {
        $session = MovieSession::findOrFail($id);

        // Borrar holds caducados de esta función
        SeatHold::where('funcion_id', $id)
            ->where('expires_at', '<=', now())
            ->delete();

        // Asientos comprados (estado = ocupada)
        $comprados = Seat::where('movieSession_id', $id)
            ->where('estado', 'ocupada')
            ->get()
            ->map(fn($s) => "{$s->fila}-{$s->numero}")
            ->values()
            ->all();

        // Asientos retenidos por otros tokens
        $holdToken = $request->query('hold_token');
        $holdsQuery = SeatHold::where('funcion_id', $id)
            ->where('expires_at', '>', now());

        if ($holdToken) {
            $holdsQuery->where('hold_token', '!=', $holdToken);
        }

        $retenidos = $holdsQuery->pluck('asiento')->values()->all();

        // Asientos retenidos por el propio token (para que el frontend verifique si sus holds siguen vivos)
        $misHolds = [];
        if ($holdToken) {
            $misHolds = SeatHold::where('funcion_id', $id)
                ->where('hold_token', $holdToken)
                ->where('expires_at', '>', now())
                ->pluck('asiento')
                ->values()
                ->all();
        }

        return response()->json([
            'funcion_id' => (int) $id,
            'comprados'  => $comprados,
            'retenidos'  => $retenidos,
            'mis_holds'  => $misHolds,
        ], 200);
    }
}
