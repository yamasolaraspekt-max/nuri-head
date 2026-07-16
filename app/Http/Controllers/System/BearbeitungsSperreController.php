<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Services\Sperre\BearbeitungsSperreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Generische Bearbeitungs-Sperre (2026-07-16) — dünne HTTP-Hülle um den
 * BearbeitungsSperreService. Sperre gilt JE DOKUMENT (Yama-Entscheid).
 */
class BearbeitungsSperreController extends Controller
{
    public function __construct(private BearbeitungsSperreService $sperre)
    {
    }

    public function ping(Request $request): JsonResponse
    {
        [$bereich, $id] = $this->parameter($request);

        return response()->json(['success' => true] + $this->sperre->ping(Auth::user(), $bereich, $id));
    }

    public function status(Request $request): JsonResponse
    {
        [$bereich, $id] = $this->parameter($request);

        return response()->json(['success' => true] + $this->sperre->status(Auth::user(), $bereich, $id));
    }

    public function leave(Request $request): JsonResponse
    {
        [$bereich, $id] = $this->parameter($request);

        return response()->json(['success' => true] + $this->sperre->leave(Auth::user(), $bereich, $id));
    }

    /** @return array{0:string,1:string} */
    private function parameter(Request $request): array
    {
        $daten = $request->validate([
            'bereich' => ['required', 'string', 'regex:/^[a-z0-9_-]{2,40}$/'],
            'id' => ['required', 'string', 'max:40'],
        ]);

        return [$daten['bereich'], $daten['id']];
    }
}
