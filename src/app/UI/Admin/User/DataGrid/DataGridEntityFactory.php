<?php

namespace App\UI\Admin\User\DataGrid;

use App\Component\Datagrid\Entity\ColumnEntity;
use App\Component\Datagrid\Entity\DataGridEntity;
use App\Component\Datagrid\Entity\DeleteMenuEntity;
use App\Component\Datagrid\Entity\MenuEntity;
use App\UI\Admin\User\UserFacade;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\Translator;
use Nette\Security\User;

readonly class DataGridEntityFactory
{
    public function __construct(
        private Translator $translator,
        private User $userSecurity,
        private UserFacade $userFacade,
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

        $dataGridEntity
            ->addMenu(new MenuEntity($this->translator->translate('menu_edit'), 'edit'))
            ->addMenu(new DeleteMenuEntity($this->translator->translate('menu_delete'), 'delete')
                ->setShowCallback(function(ActiveRow $row):bool{
                    return $this->userFacade->canByDeleted($row, $this->userSecurity);
                })
            )
        ;

        return $dataGridEntity;
    }
}
