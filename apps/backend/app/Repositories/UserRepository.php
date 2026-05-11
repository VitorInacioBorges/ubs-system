<?php

namespace App\Repositories;

use App\Models\UserModel;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function __construct(
        protected UserModel $model,
    ) {
    }

    /**
     * @return Collection<int, UserModel>
     */
    public function findAllUsers(): Collection
    {
        return $this->model->newQuery()->get();
    }

    public function findUser(string $id): ?UserModel
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createUser(array $data): UserModel
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateUser(string $id, array $data): ?UserModel
    {
        $record = $this->findUser($id);

        if ($record === null) {
            return null;
        }

        $record->fill($data);
        $record->save();

        return $record->refresh();
    }

    public function deleteUser(string $id): bool
    {
        $record = $this->findUser($id);

        if ($record === null) {
            return false;
        }

        return (bool) $record->delete();
    }

    public function deleteUserInstance(UserModel $model): bool
    {
        return (bool) $model->delete();
    }
}
