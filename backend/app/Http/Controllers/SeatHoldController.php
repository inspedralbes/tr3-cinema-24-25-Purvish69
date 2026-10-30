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
     * Dentro de una transacción con lockForUpdate:
     * - Borra holds caducados de esa función.
     * - Comprueba que los asientos no estén comprados ni retenidos por otro token.
     * - Limita el número máximo de holds activos por token a 10.
     * - Si alguno está ocupado/retenido por otro, devuelve 409 con los asientos en conflicto y no crea ninguno.
     * - Crea/renueva los holds (5 minutos).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'funcion_id' => 'required|exists:movieSessions,id',
            'asientos'   => 'required|array|min:1|max:10',
            'asientos.*' => ['required', 'string', 'max:10', 'regex:/^[A-Za-z]+-?\d+$/'],
            'hold_token' => 'required|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Parámetros de reserva no válidos',
                'errors'  => $validator->errors()
            ], 422);
        }

        $funcionId = (int) $request->funcion_id;
        $normalizedAsientos = [];
        foreach ((array) $request->asientos as $a) {
            [$fila, $numero] = $this->parseSeatCode((string) $a);
            if ($fila && $numero !== null) {
                $normalizedAsientos[] = "{$fila}-{$numero}";
            }
        }
        $asientos = array_values(array_unique($normalizedAsientos));

        if (empty($asientos)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Parámetros de reserva no válidos',
            ], 422);
        }

        $holdToken = trim($request->hold_token);

        return DB::transaction(function () use ($funcionId, $asientos, $holdToken, $request) {
            // 1. Borrar todos los holds caducados globalmente
            SeatHold::where('expires_at', '<=', now())->delete();

            // 2. Comprobar límite de holds activos por token en esta función (máximo 10)
            $existingActiveHoldsCount = SeatHold::where('hold_token', $holdToken)
                ->where('funcion_id', $funcionId)
                ->where('expires_at', '>', now())
                ->whereNotIn('asiento', $asientos)
                ->count();

            if ($existingActiveHoldsCount + count($asientos) > 10) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Límite de 10 asientos retenidos simultáneamente alcanzado'
                ], 422);
            }

            // 3. Bloqueo concurrente con lockForUpdate para evitar condiciones de carrera
            $conflicts = [];

            foreach ($asientos as $asiento) {
                [$fila, $numero] = $this->parseSeatCode($asiento);

                // Comprobar si ya está comprado en la base de datos (con lock)
                $isBought = false;
                if ($fila && $numero !== null) {
                    $isBought = Seat::where('movieSession_id', $funcionId)
                        ->where('fila', $fila)
                        ->where('numero', $numero)
                        ->where('estado', 'ocupada')
                        ->lockForUpdate()
                        ->exists();
                }

                if ($isBought) {
                    $conflicts[] = $asiento;
                    continue;
                }

                // Comprobar si está retenido por otro token activo (con lock)
                $isHeldByOther = SeatHold::where('funcion_id', $funcionId)
                    ->where('asiento', $asiento)
                    ->where('hold_token', '!=', $holdToken)
                    ->where('expires_at', '>', now())
                    ->lockForUpdate()
                    ->exists();

                if ($isHeldByOther) {
                    $conflicts[] = $asiento;
                }
            }

            // Si alguno está ocupado o retenido por otro, devuelve 409
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
            'hold_token' => 'required|string|max:64',
            'funcion_id' => 'sometimes|exists:movieSessions,id',
            'asientos'   => 'sometimes|array',
            'asientos.*' => ['string', 'max:10', 'regex:/^[A-Za-z]+-?\d+$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $holdToken = trim($request->hold_token);
        $query = SeatHold::where('hold_token', $holdToken);

        if ($request->filled('funcion_id')) {
            $query->where('funcion_id', $request->funcion_id);
        }

        if ($request->has('asientos') && is_array($request->asientos)) {
            $normalizedAsientos = [];
            foreach ($request->asientos as $a) {
                [$fila, $numero] = $this->parseSeatCode((string) $a);
                if ($fila && $numero !== null) {
                    $normalizedAsientos[] = "{$fila}-{$numero}";
                }
            }
            $query->whereIn('asiento', $normalizedAsientos);
        }

        $deletedCount = $query->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Bloqueos liberados correctamente',
            'deleted' => $deletedCount
        ], 200);
    }

    /**
     * GET /funciones/{id}/asientos
     * Devuelve asientos comprados y asientos retenidos por otros ignorando holds caducados.
     */
    public function getAsientos(Request $request, $id)
    {
        $holdToken = $request->query('hold_token', '');
        $funcionId = (int) $id;

        // Limpiar holds caducados globalmente antes de responder
        SeatHold::where('expires_at', '<=', now())
            ->delete();

        // 1. Asientos comprados de la sesión
        $comprados = Seat::where('movieSession_id', $funcionId)
            ->where('estado', 'ocupada')
            ->get(['fila', 'numero'])
            ->map(fn($s) => "{$s->fila}-{$s->numero}")
            ->values()
            ->all();

        // 2. Asientos retenidos por OTROS tokens activos
        $retenidosQuery = SeatHold::where('funcion_id', $funcionId)
            ->where('expires_at', '>', now());

        if (!empty($holdToken)) {
            $retenidosQuery->where('hold_token', '!=', $holdToken);
        }

        $retenidos = $retenidosQuery->pluck('asiento')->unique()->values()->all();

        // 3. Asientos retenidos por el token del usuario actual
        $misHolds = [];
        if (!empty($holdToken)) {
            $misHolds = SeatHold::where('funcion_id', $funcionId)
                ->where('hold_token', $holdToken)
                ->where('expires_at', '>', now())
                ->pluck('asiento')
                ->values()
                ->all();
        }

        return response()->json([
            'funcion_id' => $funcionId,
            'comprados'  => $comprados,
            'retenidos'  => $retenidos,
            'mis_holds'  => $misHolds,
        ], 200);
    }
}
