<?php

namespace App\UI\User;

use App\Component\Datagrid\DataGrid;
use App\Component\Datagrid\DataGridFactory;
use App\Model\Entity\UserEntity;
use App\Model\User;
use App\UI\Accessory\Form\Form;
use App\UI\Accessory\PresenterTrait\RequireLoggedUserTrait;
use App\UI\Accessory\PresenterTrait\StandardTemplateTrait;
use App\UI\Accessory\Submenu\SubmenuFactory;
use App\UI\User\DataGrid\DataGridEntityFactory;
use App\UI\User\Form\FormEditData;
use App\UI\User\Form\FormFactory;
use App\UI\User\Form\FormNewData;
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
        private readonly DataGridFactory       $dataGridFactory,
        private readonly User                  $userModel,
        private readonly DataGridEntityFactory $dataGridEntityFactory,
        private readonly FormFactory           $formFactory,
        private readonly UserFacade            $userFacade,
        private readonly SubmenuFactory        $submenuFactory,
    )
    {
        parent::__construct();
    }

    protected function startup():void
    {
        parent::startup();
        $this->submenuFactory->addMenu($this->translator->translate('Přidat uživatele'), $this->link('new'))
            ->setModalId('form-new')
            ->setIsPrimary(true);
    }

    private function exist(int $id):void{
        $user = $this->userModel->get($id);
        if($user === null){
            $this->flashMessage($this->translator->translate('Uživatel nebyl nalezen'), 'error');
            $this->redirect('default');
        }
        $this->user = $user;
    }

    public function actionEdit(int $id):void
    {
        $this->exist($id);
    }

    protected function createComponentFormEdit():Form
    {
        $form = $this->formFactory->createEdit($this->user);
        $form->onSuccess[] = function(Form $form, FormEditData $values):void{
            $this->userFacade->update($this->user, $values);
            $this->flashMessage($this->translator->translate('Uživatel upraven'));
            $this->redirect('default');
        };
        return $form;
    }

    protected function createComponentFormNew():Form
    {
        $form = $this->formFactory->createNew();
        $form->onSuccess[] = function(Form $form, FormNewData $values):void{
            $this->userFacade->create($values);
            $this->flashMessage($this->translator->translate('Uživatel vytvořen'));
            $this->redirect('default');
        };
        return $form;
    }

    protected function createComponentGrid():DataGrid
    {
        return $this->dataGridFactory->create($this->userModel->getTable(), $this->dataGridEntityFactory->create());
    }
}