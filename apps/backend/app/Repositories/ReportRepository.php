<?php

namespace App\Repositories;

use App\Models\ReportModel;
use Illuminate\Database\Eloquent\Collection;

class ReportRepository
{
    public function __construct(
        protected ReportModel $model,
    ) {
    }

    /**
     * @return Collection<int, ReportModel>
     */
    public function findAllReports(): Collection
    {
        return $this->model->newQuery()->get();
    }

    public function findReport(string $id): ?ReportModel
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createReport(array $data): ReportModel
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateReport(string $id, array $data): ?ReportModel
    {
        $record = $this->findReport($id);

        if ($record === null) {
            return null;
        }

        $record->fill($data);
        $record->save();

        return $record->refresh();
    }

    public function deleteReport(string $id): bool
    {
        $record = $this->findReport($id);

        if ($record === null) {
            return false;
        }

        return (bool) $record->delete();
    }

    public function deleteReportInstance(ReportModel $model): bool
    {
        return (bool) $model->delete();
    }
}
