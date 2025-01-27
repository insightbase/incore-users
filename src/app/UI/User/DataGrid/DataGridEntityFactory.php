<?php

namespace App\UI\User\DataGrid;

use App\Component\Datagrid\Entity\ColumnEntity;
use App\Component\Datagrid\Entity\DataGridEntity;
use App\Component\Datagrid\Entity\MenuEntity;
use Nette\Localization\Translator;

readonly class DataGridEntityFactory
{
    public function __construct(
        private Translator $translator,
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
        ;

        $dataGridEntity
            ->addMenu(new MenuEntity($this->translator->translate('menu_edit'), 'edit'))
        ;

        return $dataGridEntity;
    }
}
