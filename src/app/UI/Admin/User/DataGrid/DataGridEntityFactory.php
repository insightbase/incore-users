<?php

namespace App\UI\Admin\User\DataGrid;

use App\Component\Datagrid\Entity\ColumnEntity;
use App\Component\Datagrid\Entity\DataGridEntity;
use App\Component\Datagrid\Entity\DeleteMenuEntity;
use App\Component\Datagrid\Entity\FilterEntity;
use App\Component\Datagrid\Entity\MenuEntity;
use App\Component\Datagrid\Enum\FilterTypeEnum;
use App\Core\Admin\Impersonation\ImpersonationFacade;
use App\Model\Admin\Role;
use App\UI\Admin\User\UserFacade;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Localization\Translator;
use Nette\Security\User;

readonly class DataGridEntityFactory
{
    public function __construct(
        private Translator $translator,
        private User $userSecurity,
        private UserFacade $userFacade,
        private Role $roleModel,
        private ImpersonationFacade $impersonationFacade,
    ) {}

    public function create(): DataGridEntity
    {
        $dataGridEntity = new DataGridEntity();
        $dataGridEntity
            ->addColumn(new ColumnEntity('id', $this->translator->translate('id')))
            ->addColumn((new ColumnEntity('firstname', $this->translator->translate('column_firstname'), true))
                ->setEnableSearchGlobal(true))
            ->addColumn((new ColumnEntity('lastname', $this->translator->translate('column_lastname')))
                ->setEnableSearchGlobal(true))
            ->addColumn(
                (new ColumnEntity('email', $this->translator->translate('column_email')))
                    ->setEnableSearchGlobal(true)
            )
            ->addColumn(
                (new ColumnEntity('name', $this->translator->translate('column_role'), true))
                    ->setRef(['role'])
                    ->setSortString(fn(): string => 'role.name')
            )
        ;

        $roles = [];
        foreach ($this->roleModel->getToSelect() as $role) {
            $roles[$role->id] = $role->name;
        }
        $dataGridEntity->addFilter(
            (new FilterEntity($this->translator->translate('filter_role'), FilterTypeEnum::Select, $roles))
                ->setOnChangeCallback(function (Selection $model, string $value): void {
                    if ($value !== '') {
                        $model->where('role_id', $value);
                    }
                })
        );

        $dataGridEntity
            ->addMenu(new MenuEntity($this->translator->translate('menu_edit'), 'edit'))
            ->addMenu((new MenuEntity($this->translator->translate('menu_impersonate'), 'impersonate'))
                ->setIcon('ki-filled ki-user')
                ->setShowCallback(fn(ActiveRow $row): bool => $this->impersonationFacade->canImpersonate($row))
            )
            ->addMenu(new DeleteMenuEntity($this->translator->translate('menu_delete'), 'delete')
                ->setShowCallback(function(ActiveRow $row):bool{
                    return $this->userFacade->canByDeleted($row, $this->userSecurity);
                })
            )
        ;

        return $dataGridEntity;
    }
}
