<?php

namespace Src\Agenda\User\Application\UseCases\Commands;

use Src\Agenda\User\Application\Exceptions\EmailAlreadyUsedException;
use Src\Agenda\User\Domain\Model\User;
use Src\Agenda\User\Domain\Model\ValueObjects\Password;
use Src\Agenda\User\Domain\Policies\UserPolicy;
use Src\Agenda\User\Domain\Repositories\AvatarRepositoryInterface;
use Src\Agenda\User\Domain\Repositories\UserRepositoryInterface;
use Src\Agenda\User\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Common\Domain\CommandInterface;

class StoreUserCommand implements CommandInterface
{
    private UserRepositoryInterface $repository;
    // private AvatarRepositoryInterface $avatarRepository;

    public function __construct(
        private readonly User $user,
        private readonly Password $password
    )
    {
        $this->repository = app()->make(UserRepositoryInterface::class);
        // $this->avatarRepository = app()->make(AvatarRepositoryInterface::class);
    }

    public function execute(): User
    {
        authorize('store', UserPolicy::class);
        if (UserEloquentModel::query()->where('email', $this->user->email)->exists()) {
            throw new EmailAlreadyUsedException();
        }

        // $avatar = $this->user->avatar;
        // if ($avatar->hasBinaryData()) {
        //     $filename = $this->avatarRepository->storeAvatarFile($avatar);
        //     $this->user->setAvatar($avatar->binary_data, $filename);
        // }

        return $this->repository->store($this->user, $this->password);
    }
}
