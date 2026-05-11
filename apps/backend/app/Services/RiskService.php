<?php

namespace App\Services;

use App\Models\RiskModel;
use App\Repositories\RiskRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RiskService
{
    public function __construct(
        protected RiskRepository $repository,
    ) {
    }

    public function getAllRisks()
    {
        return $this->repository->findAllRisks();
    }

    public function getRisk(string $id): RiskModel
    {
        $risk = $this->repository->findRisk($id);

        if ($risk === null) {
            throw (new ModelNotFoundException())->setModel(RiskModel::class, [$id]);
        }

        return $risk;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createRisk(array $data): RiskModel
    {
        return $this->repository->createRisk($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateRisk(string $id, array $data): RiskModel
    {
        $this->getRiskOrFail($id);

        $risk = $this->repository->updateRisk($id, $data);

        if ($risk === null) {
            throw (new ModelNotFoundException())->setModel(RiskModel::class, [$id]);
        }

        return $risk;
    }

    public function deleteRisk(string $id): bool
    {
        $risk = $this->getRiskOrFail($id);

        return $this->repository->deleteRiskInstance($risk);
    }

    public function deleteRiskInstance(RiskModel $risk): bool
    {
        return $this->repository->deleteRiskInstance($risk);
    }
}
