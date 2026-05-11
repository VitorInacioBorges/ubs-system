<?php

namespace App\Repositories;

use App\Models\PatientModel;
use Illuminate\Database\Eloquent\Collection;

class PatientRepository
{
    public function __construct(
        protected PatientModel $model,
    ) {
    }

    /**
     * @return Collection<int, PatientModel>
     */
    public function findAllPatients(): Collection
    {
        return $this->model->newQuery()->get();
    }

    public function findPatient(string $id): ?PatientModel
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createPatient(array $data): PatientModel
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updatePatient(string $id, array $data): ?PatientModel
    {
        $record = $this->findPatient($id);

        if ($record === null) {
            return null;
        }

        $record->fill($data);
        $record->save();

        return $record->refresh();
    }

    public function deletePatient(string $id): bool
    {
        $record = $this->findPatient($id);

        if ($record === null) {
            return false;
        }

        return (bool) $record->delete();
    }

    public function deletePatientInstance(PatientModel $model): bool
    {
        return (bool) $model->delete();
    }
}
