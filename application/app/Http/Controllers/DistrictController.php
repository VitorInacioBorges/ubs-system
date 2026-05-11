<?php

namespace App\Http\Controllers;

use App\Services\DistrictService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function __construct(
        protected DistrictService $service,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->service->getAllDistricts((int) $request->query('per_page', 20)));
    }

    public function show(string $id): JsonResponse
    {
        return response()->json($this->service->getDistrictById($id));
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json($this->service->createDistrict($request->all()), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json($this->service->updateDistrict($id, $request->all()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->service->deleteDistrict($id);

        return response()->json(null, 204);
    }

    public function deleteSelf(string $id): JsonResponse
    {
        $this->service->deleteDistrict($id);

        return response()->json(null, 204);
    }
}
