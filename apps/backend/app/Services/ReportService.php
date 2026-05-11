<?php

namespace App\Services;

use App\Models\ReportModel;
use App\Repositories\ReportRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReportService
{
    public function __construct(
        protected ReportRepository $repository,
    ) {
    }

    public function getAllReports()
    {
        return $this->repository->findAllReports();
    }

    public function getReport(string $id): ReportModel
    {
        $report = $this->repository->findReport($id);

        if ($report === null) {
            throw (new ModelNotFoundException())->setModel(ReportModel::class, [$id]);
        }

        return $report;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createReport(array $data): ReportModel
    {
        return $this->repository->createReport($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateReport(string $id, array $data): ReportModel
    {
        $this->getReportOrFail($id);

        $report = $this->repository->updateReport($id, $data);

        if ($report === null) {
            throw (new ModelNotFoundException())->setModel(ReportModel::class, [$id]);
        }

        return $report;
    }

    public function deleteReport(string $id): bool
    {
        $report = $this->getReportOrFail($id);

        return $this->repository->deleteReportInstance($report);
    }

    public function deleteReportInstance(ReportModel $report): bool
    {
        return $this->repository->deleteReportInstance($report);
    }
}
