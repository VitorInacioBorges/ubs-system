<?php

namespace App\Services;

use App\Models\AssessmentModel;
use App\Repositories\AssessmentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AssessmentService
{
    public function __construct(
        protected AssessmentRepository $repository,
    ) {
    }

    public function getAllAssessments()
    {
        return $this->repository->findAllAssessments();
    }

    public function getAssessment(string $id): AssessmentModel
    {
        $assessment = $this->repository->findAssessment($id);

        if ($assessment === null) {
            throw (new ModelNotFoundException())->setModel(AssessmentModel::class, [$id]);
        }

        return $assessment;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createAssessment(array $data): AssessmentModel
    {
        return $this->repository->createAssessment($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateAssessment(string $id, array $data): AssessmentModel
    {
        $this->getAssessmentOrFail($id);

        $assessment = $this->repository->updateAssessment($id, $data);

        if ($assessment === null) {
            throw (new ModelNotFoundException())->setModel(AssessmentModel::class, [$id]);
        }

        return $assessment;
    }

    public function deleteAssessment(string $id): bool
    {
        $assessment = $this->getAssessmentOrFail($id);

        return $this->repository->deleteAssessmentInstance($assessment);
    }

    public function deleteAssessmentInstance(AssessmentModel $assessment): bool
    {
        return $this->repository->deleteAssessmentInstance($assessment);
    }
}
