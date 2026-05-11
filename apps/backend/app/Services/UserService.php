<?php

namespace App\Services;

use App\Models\UserModel;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    public function __construct(
        protected UserRepository $repository,
    ) {
    }

    public function getAllUsers()
    {
        return $this->repository->findAllUsers();
    }

    public function getUser(string $id): UserModel
    {
        $user = $this->repository->findUser($id);

        if ($user === null) {
            throw (new ModelNotFoundException())->setModel(UserModel::class, [$id]);
        }

        return $user;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createUser(array $data): UserModel
    {
        return $this->repository->createUser($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateUser(string $id, array $data): UserModel
    {
        $this->getUserOrFail($id);

        $user = $this->repository->updateUser($id, $data);

        if ($user === null) {
            throw (new ModelNotFoundException())->setModel(UserModel::class, [$id]);
        }

        return $user;
    }

    public function deleteUser(string $id): bool
    {
        $user = $this->getUserOrFail($id);

        return $this->repository->deleteUserInstance($user);
    }

    public function deleteUserInstance(UserModel $user): bool
    {
        return $this->repository->deleteUserInstance($user);
    }
}
