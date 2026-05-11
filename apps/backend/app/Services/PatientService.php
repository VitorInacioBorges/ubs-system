<?php

namespace App\Services;

use App\Models\PatientModel;
use App\Repositories\PatientRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PatientService
{
    public function __construct(
        protected PatientRepository $repository,
    ) {
    }

    public function getAllPatients()
    {
        return $this->repository->findAllPatients();
    }

    public function getPatient(string $id): PatientModel
    {
        $patient = $this->repository->findPatient($id);

        if ($patient === null) {
            throw (new ModelNotFoundException())->setModel(PatientModel::class, [$id]);
        }

        return $patient;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createPatient(array $data): PatientModel
    {
        return $this->repository->createPatient($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updatePatient(string $id, array $data): PatientModel
    {
        $this->getPatientOrFail($id);

        $patient = $this->repository->updatePatient($id, $data);

        if ($patient === null) {
            throw (new ModelNotFoundException())->setModel(PatientModel::class, [$id]);
        }

        return $patient;
    }

    public function deletePatient(string $id): bool
    {
        $patient = $this->getPatientOrFail($id);

        return $this->repository->deletePatientInstance($patient);
    }

    public function deletePatientInstance(PatientModel $patient): bool
    {
        return $this->repository->deletePatientInstance($patient);
    }
}
