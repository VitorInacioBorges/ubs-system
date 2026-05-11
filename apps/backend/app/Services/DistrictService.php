<?php

namespace App\Services;

use App\Models\DistrictModel;
use App\Repositories\DistrictRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DistrictService
{
    public function __construct(
        protected DistrictRepository $repository,
    ) {
    }

    public function getAllDistricts()
    {
        return $this->repository->findAllDistricts();
    }

    public function getDistrict(string $id): DistrictModel
    {
        $district = $this->repository->findDistrict($id);

        if ($district === null) {
            throw (new ModelNotFoundException())->setModel(DistrictModel::class, [$id]);
        }

        return $district;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createDistrict(array $data): DistrictModel
    {
        return $this->repository->createDistrict($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateDistrict(string $id, array $data): DistrictModel
    {
        $this->getDistrictOrFail($id);

        $district = $this->repository->updateDistrict($id, $data);

        if ($district === null) {
            throw (new ModelNotFoundException())->setModel(DistrictModel::class, [$id]);
        }

        return $district;
    }

    public function deleteDistrict(string $id): bool
    {
        $district = $this->getDistrictOrFail($id);

        return $this->repository->deleteDistrictInstance($district);
    }

    public function deleteDistrictInstance(DistrictModel $district): bool
    {
        return $this->repository->deleteDistrictInstance($district);
    }
}
