<?php

namespace App\UI\Admin\User;

use App\Component\Log\LogActionEnum;
use App\Component\Log\LogFacade;
use App\Model\Admin\User;
use App\Model\Entity\UserEntity;
use App\Model\Enum\RoleEnum;
use App\UI\Admin\User\Exception\UserCannotByDeletedException;
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
        private \Nette\Security\User $userSecurity,
    ) {}

    public function canByDeleted(ActiveRow $user, \Nette\Security\User $userSecurity):bool
    {
        if($userSecurity->isInRole(RoleEnum::SUPER_ADMIN->value)){
            if($user['id'] !== $userSecurity->getId()){
                return true;
            }
        }elseif($userSecurity->isInRole(RoleEnum::ADMIN->value)){
            if($user['id'] !== $userSecurity->getId() && $user->ref('role')['system_name'] !== RoleEnum::SUPER_ADMIN->value){
                return true;
            }
        }
        return false;
    }

    public function create(FormNewData $values): void
    {
        $user = $this->userModel->insert([
            'firstname' => $values->firstname,
            'lastname' => $values->lastname,
            'email' => $values->email,
            'password' => $this->passwords->hash($values->password),
            'role_id' => $values->role_id,
            'dropcore_identity_token' => $values->dropcore_identity_token,
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
            'dropcore_identity_token' => $values->dropcore_identity_token,
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

    /**
     * @param UserEntity $user
     * @return void
     * @throws UserCannotByDeletedException
     */
    public function delete(ActiveRow $user):void
    {
        if(!$this->canByDeleted($user, $this->userSecurity)){
            throw new UserCannotByDeletedException();
        }
        $user->delete();
    }
}
