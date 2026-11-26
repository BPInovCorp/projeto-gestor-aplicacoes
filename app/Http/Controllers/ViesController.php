<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarViesRequest;
use App\Services\ViesService;
use Illuminate\Http\JsonResponse;

class ViesController extends Controller
{
    public function __construct(
        private readonly ViesService $viesService
    ) {}

    public function __invoke(ValidarViesRequest $request): JsonResponse
    {
        $resultado = $this->viesService->validar(
            $request->validated('codigo_pais'),
            $request->validated('nif')
        );

        return response()->json($resultado, $resultado['valido'] ? 200 : 422);
    }
}