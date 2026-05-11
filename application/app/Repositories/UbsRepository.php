<?php

namespace App\Repositories;

use App\Models\UbsModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UbsRepository
{
    public function __construct(
        protected UbsModel $model,
    ) {
    }

    public function paginateUbs(int $perPage): LengthAwarePaginator
    {
        return $this->model->newQuery()->paginate($perPage);
    }

    public function findUbsById(string $id): ?UbsModel
    {
        return $this->model->newQuery()->find($id);
    }

    public function findUbsByEmail(string $email): ?UbsModel
    {
        return $this->model->newQuery()
            ->where('email', $email)
            ->first();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createUbs(array $data): UbsModel
    {
        return $this->model->newQuery()->create($data);
    }
}
