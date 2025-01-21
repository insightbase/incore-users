<?php

namespace App\UI\User;

use App\Model\Entity\UserEntity;
use App\Model\Enum\RoleEnum;
use App\Model\Role;
use App\Model\User;
use App\UI\User\Form\FormNewData;
use Nette\Database\Table\ActiveRow;
use Nette\Security\Passwords;

readonly class UserFacade
{
    public function __construct(
        private User      $userModel,
        private Passwords $passwords,
        private Role      $roleModel,
    )
    {
    }

    public function create(FormNewData $values):void
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
     * @param \UI\User\Form\FormEditData $values
     * @return void
     */
    public function update(ActiveRow $user, \UI\User\Form\FormEditData $values):void
    {
        $user->update([
            'firstname' => $values->firstname,
            'lastname' => $values->lastname,
            'email' => $values->email,
        ]);
    }
}