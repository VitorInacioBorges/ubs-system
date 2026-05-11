<?php

namespace App\Repositories;

use App\Models\AssessmentModel;
use Illuminate\Database\Eloquent\Collection;

class AssessmentRepository
{
    public function __construct(
        protected AssessmentModel $model,
    ) {
    }

    /**
     * @return Collection<int, AssessmentModel>
     */
    public function findAllAssessments(): Collection
    {
        return $this->model->newQuery()->get();
    }

    public function findAssessment(string $id): ?AssessmentModel
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createAssessment(array $data): AssessmentModel
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateAssessment(string $id, array $data): ?AssessmentModel
    {
        $record = $this->findAssessment($id);

        if ($record === null) {
            return null;
        }

        $record->fill($data);
        $record->save();

        return $record->refresh();
    }

    public function deleteAssessment(string $id): bool
    {
        $record = $this->findAssessment($id);

        if ($record === null) {
            return false;
        }

        return (bool) $record->delete();
    }

    public function deleteAssessmentInstance(AssessmentModel $model): bool
    {
        return (bool) $model->delete();
    }
}
