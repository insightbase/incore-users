<?php

namespace App\UI\Admin\User;

use App\Component\Datagrid\DataGrid;
use App\Component\Datagrid\DataGridFactory;
use App\Model\Admin\User;
use App\Model\Entity\UserEntity;
use App\UI\Accessory\Admin\Form\Form;
use App\UI\Accessory\Admin\PresenterTrait\RequireLoggedUserTrait;
use App\UI\Accessory\Admin\PresenterTrait\StandardTemplateTrait;
use App\UI\Accessory\Admin\Submenu\SubmenuFactory;
use App\UI\Admin\User\DataGrid\DataGridEntityFactory;
use App\UI\Admin\User\Form\ChangePasswordData;
use App\UI\Admin\User\Form\FormEditData;
use App\UI\Admin\User\Form\FormFactory;
use App\UI\Admin\User\Form\FormNewData;
use Nette\Application\UI\Presenter;
use Nette\Database\Table\ActiveRow;

final class UserPresenter extends Presenter
{
    use StandardTemplateTrait;
    use RequireLoggedUserTrait;

    /**
     * @var UserEntity
     */
    private ActiveRow $user;

    public function __construct(
        private readonly DataGridFactory $dataGridFactory,
        private readonly User $userModel,
        private readonly DataGridEntityFactory $dataGridEntityFactory,
        private readonly FormFactory $formFactory,
        private readonly UserFacade $userFacade,
        private readonly SubmenuFactory $submenuFactory,
    ) {
        parent::__construct();
    }

    protected function createComponentFormChangePassword():Form
    {
        $form = $this->formFactory->createChangePassword();
        $form->onSuccess[] = function(Form $form, ChangePasswordData $data):void{
            $this->userFacade->changePassword($this->user, $data);
            $this->flashMessage($this->translator->translate('flash_passwordChanged'));
            $this->redirect('default');
        };
        return $form;
    }

    public function actionEdit(int $id): void
    {
        $this->exist($id);
    }

    public function actionDelete(int $id): void
    {
        $this->exist($id);

        try {
            $this->userFacade->delete($this->user);
            $this->flashMessage($this->translator->translate('flash_userDeleted'));
        } catch (Exception\UserCannotByDeletedException $e) {
            $this->flashMessage($this->translator->translate('flash_userCannotByDeleted'), 'error');
        }
        $this->redirect('default');
    }

    protected function startup(): void
    {
        parent::startup();
        $this->submenuFactory->addMenu($this->translator->translate('menu_newUser'), 'new')
            ->setModalId('form-new')
            ->setIsPrimary(true)
        ;
    }

    protected function createComponentFormEdit(): Form
    {
        $form = $this->formFactory->createEdit($this->user);
        $form->onSuccess[] = function (Form $form, FormEditData $values): void {
            $this->userFacade->update($this->user, $values);
            $this->flashMessage($this->translator->translate('flash_userUpdated'));
            $this->redirect('default');
        };

        return $form;
    }

    protected function createComponentFormNew(): Form
    {
        $form = $this->formFactory->createNew();
        $form->onSuccess[] = function (Form $form, FormNewData $values): void {
            $this->userFacade->create($values);
            $this->flashMessage($this->translator->translate('flash_userCreated'));

            $this->redirect('default');
        };
        $form->onError[] = function():void{
            $this->redrawControl('formNew');
        };

        return $form;
    }

    protected function createComponentGrid(): DataGrid
    {
        return $this->dataGridFactory->create($this->userModel->getToGrid(), $this->dataGridEntityFactory->create());
    }

    private function exist(int $id): void
    {
        $user = $this->userModel->get($id);
        if (null === $user) {
            $this->flashMessage($this->translator->translate('flash_userNotFound'), 'error');
            $this->redirect('default');
        }
        $this->user = $user;
    }
}
