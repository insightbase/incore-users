<?php

namespace App\UI\Admin\User\Form;

use App\Component\Translator\Translator;
use App\Model\Admin\Role;
use App\Model\Admin\User;
use App\Model\Entity\UserEntity;
use App\UI\Accessory\Admin\Form\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\TextInput;

class FormFactory
{
    /**
     * @var ?UserEntity
     */
    private ?ActiveRow $user = null;

    public function __construct(
        private readonly \App\UI\Accessory\Admin\Form\FormFactory $formFactory,
        private readonly Translator                               $translator,
        private readonly User                                     $userModel,
        private readonly Role                                     $roleModel,
    ) {}

    public function createChangePassword():Form
    {
        $form = $this->formFactory->create();

        $form->addPassword('password', $this->translator->translate('input_newPassword'))
            ->setRequired();
        $form->addSubmit('send', $this->translator->translate('input_set'));

        return $form;
    }

    public function validateEmail(TextInput $input): bool
    {
        $userId = null;
        if (null !== $this->user) {
            $userId = $this->user->id;
        }
        $user = $this->userModel->findByEmail($input->getValue(), $userId);

        return null === $user;
    }

    public function createNew(): Form
    {
        $form = $this->createBase();
        $form->sendByAjax();
        $form->addPassword('password', $this->translator->translate('input_password'))
            ->setRequired()
        ;
        $form->addSubmit('send', $this->translator->translate('submit_create'));

        return $form;
    }

    /**
     * @param UserEntity $userEntity
     */
    public function createEdit(ActiveRow $userEntity): Form
    {
        $this->user = $userEntity;

        $form = $this->createBase();
        $form->addSubmit('send', $this->translator->translate('submit_update'));

        $form->setDefaults([
            'firstname' => $userEntity->firstname,
            'lastname' => $userEntity->lastname,
            'email' => $userEntity->email,
            'dropcore_identity_token' => $userEntity->dropcore_identity_token,
        ]);

        return $form;
    }

    private function createBase(): Form
    {
        $form = $this->formFactory->create();

        $form->addText('firstname', $this->translator->translate('input_firstname'))
            ->setRequired()
        ;
        $form->addText('lastname', $this->translator->translate('input_lastname'))
            ->setRequired()
        ;
        $form->addEmail('email', $this->translator->translate('input_email'))
            ->addRule([$this, 'validateEmail'], $this->translator->translate('error_emailAlreadyExists'))
            ->setRequired()
        ;
        $form->addSelect('role_id', $this->translator->translate('input_role'), $this->roleModel->getToSelect()->fetchPairs('id', 'name'));
        $form->addText('dropcore_identity_token', $this->translator->translate('input_userDropCoreIdentityToken'))
            ->setNullable()
        ;

        $form->applyMaxLengthFromEntity(\App\Model\DoctrineEntity\User::class);

        return $form;
    }
}
