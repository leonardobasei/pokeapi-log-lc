<?php

namespace App\Http\Controllers;

use App\Services\PokemonIntegrationService;
use App\Services\PokemonService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PokemonController extends Controller
{
    public function getAll(Request $request, PokemonService $service): JsonResponse
    {
        try {
            $name = $request->query('name');
            $type = $request->query('type');
            $perPage = $request->query('limit');
            $page = $request->query('page');

            $perPage = $perPage !== null ? (int) $perPage : null;
            $page = $page !== null ? (int) $page : null;

            $data = $service->getAll($name, $type, $perPage, $page);
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json(["error" => $th->getMessage()], 400);
        }
    }

    public function getByName(string $name, PokemonService $service): JsonResponse
    {
        try {
            $data = $service->getByName($name);
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json(["error" => $th->getMessage()], 400);
        }
    }
}
