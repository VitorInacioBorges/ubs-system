<?php

namespace App\Repositories;

use App\Models\UbsModel;
use Illuminate\Database\Eloquent\Collection;

class UbsRepository
{
    public function __construct(
        protected UbsModel $model,
    ) {
    }

    /**
     * @return Collection<int, UbsModel>
     */
    public function findAllUbs(): Collection
    {
        return $this->model->newQuery()->get();
    }

    public function findUbs(string $id): ?UbsModel
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createUbs(array $data): UbsModel
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateUbs(string $id, array $data): ?UbsModel
    {
        $record = $this->findUbs($id);

        if ($record === null) {
            return null;
        }

        $record->fill($data);
        $record->save();

        return $record->refresh();
    }

    public function deleteUbs(string $id): bool
    {
        $record = $this->findUbs($id);

        if ($record === null) {
            return false;
        }

        return (bool) $record->delete();
    }

    public function deleteUbsInstance(UbsModel $model): bool
    {
        return (bool) $model->delete();
    }
}
