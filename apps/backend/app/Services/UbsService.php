<?php

namespace App\Services;

use App\Models\UbsModel;
use App\Repositories\UbsRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UbsService
{
    public function __construct(
        protected UbsRepository $repository,
    ) {
    }

    public function getAllUbs()
    {
        return $this->repository->findAllUbs();
    }

    public function getUbs(string $id): UbsModel
    {
        $ubs = $this->repository->findUbs($id);

        if ($ubs === null) {
            throw (new ModelNotFoundException())->setModel(UbsModel::class, [$id]);
        }

        return $ubs;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createUbs(array $data): UbsModel
    {
        return $this->repository->createUbs($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateUbs(string $id, array $data): UbsModel
    {
        $this->getUbsOrFail($id);

        $ubs = $this->repository->updateUbs($id, $data);

        if ($ubs === null) {
            throw (new ModelNotFoundException())->setModel(UbsModel::class, [$id]);
        }

        return $ubs;
    }

    public function deleteUbs(string $id): bool
    {
        $ubs = $this->getUbsOrFail($id);

        return $this->repository->deleteUbsInstance($ubs);
    }

    public function deleteUbsInstance(UbsModel $ubs): bool
    {
        return $this->repository->deleteUbsInstance($ubs);
    }
}
