<?php

namespace App\Repositories;

use App\Models\PatientModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PatientRepository
{
    public function __construct(
        protected PatientModel $model,
    ) {
    }

    public function paginatePatients(int $perPage): LengthAwarePaginator
    {
        return $this->model->newQuery()->paginate($perPage);
    }

    public function findPatientById(string $id): ?PatientModel
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
}
