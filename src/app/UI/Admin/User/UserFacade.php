<?php

namespace App\UI\Admin\User;

use App\Model\Admin\Role;
use App\Model\Admin\User;
use App\Model\Entity\UserEntity;
use App\Model\Enum\RoleEnum;
use App\UI\Admin\User\Form\FormNewData;
use Nette\Database\Table\ActiveRow;
use Nette\Security\Passwords;
use UI\User\Form\FormEditData;

readonly class UserFacade
{
    public function __construct(
        private User $userModel,
        private Passwords $passwords,
        private Role $roleModel,
    ) {}

    public function create(FormNewData $values): void
    {
        $role = $this->roleModel->findBySystemName(RoleEnum::ADMIN->value);

        $this->userModel->insert([
            'firstname' => $values->firstname,
            'lastname' => $values->lastname,
            'email' => $values->email,
            'password' => $this->passwords->hash($values->password),
            'role_id' => $role->id,
        ]);
    }

    /**
     * @param UserEntity $user
     */
    public function update(ActiveRow $user, FormEditData $values): void
    {
        $user->update([
            'firstname' => $values->firstname,
            'lastname' => $values->lastname,
            'email' => $values->email,
        ]);
    }
}
