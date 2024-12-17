<?php

namespace App\UI\User\Form;

use App\Component\Translator\Translator;
use App\Model\Entity\UserEntity;
use App\UI\Accessory\Form\Form;
use Nette\Database\Table\ActiveRow;

readonly class FormFactory
{
    public function __construct(
        private \App\UI\Accessory\Form\FormFactory $formFactory,
        private Translator                         $translator,
    )
    {
    }

    private function createBase():Form{
        $form = $this->formFactory->create();

        $form->addText('firstname', $this->translator->translate('Jméno'))
            ->setRequired();
        $form->addText('lastname', $this->translator->translate('Příjmení'))
            ->setRequired();
        $form->addEmail('email', $this->translator->translate('Email'))
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