<?php

namespace App\Repositories;

use App\Models\RiskModel;
use Illuminate\Database\Eloquent\Collection;

class RiskRepository
{
    public function __construct(
        protected RiskModel $model,
    ) {
    }

    /**
     * @return Collection<int, RiskModel>
     */
    public function findAllRisks(): Collection
    {
        return $this->model->newQuery()->get();
    }

    public function findRisk(string $id): ?RiskModel
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createRisk(array $data): RiskModel
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateRisk(string $id, array $data): ?RiskModel
    {
        $record = $this->findRisk($id);

        if ($record === null) {
            return null;
        }

        $record->fill($data);
        $record->save();

        return $record->refresh();
    }

    public function deleteRisk(string $id): bool
    {
        $record = $this->findRisk($id);

        if ($record === null) {
            return false;
        }

        return (bool) $record->delete();
    }

    public function deleteRiskInstance(RiskModel $model): bool
    {
        return (bool) $model->delete();
    }
}
