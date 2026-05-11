<?php

namespace App\Repositories;

use App\Models\DistrictModel;
use Illuminate\Database\Eloquent\Collection;

class DistrictRepository
{
    public function __construct(
        protected DistrictModel $model,
    ) {
    }

    /**
     * @return Collection<int, DistrictModel>
     */
    public function findAllDistricts(): Collection
    {
        return $this->model->newQuery()->get();
    }

    public function findDistrict(string $id): ?DistrictModel
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createDistrict(array $data): DistrictModel
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateDistrict(string $id, array $data): ?DistrictModel
    {
        $record = $this->findDistrict($id);

        if ($record === null) {
            return null;
        }

        $record->fill($data);
        $record->save();

        return $record->refresh();
    }

    public function deleteDistrict(string $id): bool
    {
        $record = $this->findDistrict($id);

        if ($record === null) {
            return false;
        }

        return (bool) $record->delete();
    }

    public function deleteDistrictInstance(DistrictModel $model): bool
    {
        return (bool) $model->delete();
    }
}
