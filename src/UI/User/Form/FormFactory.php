<?php

namespace App\UI\User\Form;

use App\Component\Translator\Translator;
use App\Model\Entity\UserEntity;
use App\Model\User;
use App\UI\Accessory\Form\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\TextInput;

class FormFactory
{
    /**
     * @var ?UserEntity
     */
    private ?ActiveRow $user = null;

    public function __construct(
        private \App\UI\Accessory\Form\FormFactory $formFactory,
        private Translator                         $translator,
        private User $userModel,
    )
    {
    }

    public function validateEmail(TextInput $input):bool
    {
        $userId = null;
        if($this->user !== null){
            $userId = $this->user->id;
        }
        $user = $this->userModel->findByEmail($input->getValue(), $userId);
        return $user === null;
    }

    private function createBase():Form{
        $form = $this->formFactory->create();

        $form->addText('firstname', $this->translator->translate('Jméno'))
            ->setRequired();
        $form->addText('lastname', $this->translator->translate('Příjmení'))
            ->setRequired();
        $form->addEmail('email', $this->translator->translate('Email'))
            ->addRule([$this, 'validateEmail'], $this->translator->translate('Takový email v systému už existuje'))
            ->setRequired();

        return $form;
    }

    public function createNew():Form
    {
        $form = $this->createBase();
        $form->addPassword('password', $this->translator->translate('Heslo'))
            ->setRequired();
        $form->addSubmit('send', $this->translator->translate('Vytvořit'));

        return $form;
    }

    /**
     * @param UserEntity $userEntity
     * @return Form
     */
    public function createEdit(ActiveRow $userEntity):Form
    {
        $this->user = $userEntity;

        $form = $this->createBase();
        $form->addSubmit('send', $this->translator->translate('Upravit'));

        $form->setDefaults([
            'firstname' => $userEntity->firstname,
            'lastname' => $userEntity->lastname,
            'email' => $userEntity->email,
        ]);

        return $form;
    }
}