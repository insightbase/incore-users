<?php

namespace App\UI\Admin\User;

use App\Model\Admin\User;
use App\Model\Entity\UserEntity;
use App\UI\Admin\User\Form\FormEditData;
use App\UI\Admin\User\Form\FormNewData;
use Nette\Database\Table\ActiveRow;
use Nette\Security\Passwords;

readonly class UserFacade
{
    public function __construct(
        private User $userModel,
        private Passwords $passwords,
    ) {}

    public function create(FormNewData $values): void
    {
        $this->userModel->insert([
            'firstname' => $values->firstname,
            'lastname' => $values->lastname,
            'email' => $values->email,
            'password' => $this->passwords->hash($values->password),
            'role_id' => $values->role_id,
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
