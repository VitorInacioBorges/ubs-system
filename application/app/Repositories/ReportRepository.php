<?php

namespace App\Repositories;

use App\Models\ReportModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReportRepository
{
    public function __construct(
        protected ReportModel $model,
    ) {
    }

    public function paginateReports(int $perPage): LengthAwarePaginator
    {
        return $this->model->newQuery()->paginate($perPage);
    }

    public function findReportById(string $id): ?ReportModel
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
}
