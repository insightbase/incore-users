<?php

namespace App\UI\Admin\User;

use App\Component\Log\LogActionEnum;
use App\Component\Log\LogFacade;
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
        private LogFacade $logFacade,
    ) {}

    public function create(FormNewData $values): void
    {
        $user = $this->userModel->insert([
            'firstname' => $values->firstname,
            'lastname' => $values->lastname,
            'email' => $values->email,
            'password' => $this->passwords->hash($values->password),
            'role_id' => $values->role_id,
        ]);
        $this->logFacade->create(LogActionEnum::Created, 'user', $user->id);
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
        $this->logFacade->create(LogActionEnum::Updated, 'user', $user->id);
    }

    /**
     * @param UserEntity $user
     * @param Form\ChangePasswordData $data
     * @return void
     */
    public function changePassword(ActiveRow $user, Form\ChangePasswordData $data):void
    {
        $user->update(['password' => $this->passwords->hash($data->password)]);
    }
}
