<?php

namespace App\Http\Controllers;

use App\Services\UbsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UbsController extends Controller
{
    public function __construct(
        protected UbsService $service,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->service->getAllUbs((int) $request->query('per_page', 20)));
    }

    public function show(string $id): JsonResponse
    {
        return response()->json($this->service->getUbsById($id));
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json($this->service->createUbs($request->all()), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json($this->service->updateUbs($id, $request->all()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->service->deleteUbs($id);

        return response()->json(null, 204);
    }

    public function deleteSelf(string $id): JsonResponse
    {
        $this->service->deleteUbs($id);

        return response()->json(null, 204);
    }
}
