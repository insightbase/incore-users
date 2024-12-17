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
    )
    {

    }

    public function create():DataGridEntity
    {
        $dataGridEntity = new DataGridEntity();
        $dataGridEntity
            ->addColumn(new ColumnEntity('id', $this->translator->translate('ID')))
            ->addColumn((new ColumnEntity('firstname', $this->translator->translate('Jméno'), true))
                ->setEnableSearchGlobal(true))
            ->addColumn((new ColumnEntity('lastname', $this->translator->translate('Příjmení')))
                ->setEnableSearchGlobal(true))
            ->addColumn((new ColumnEntity('email', $this->translator->translate('Email')))
                ->setEnableSearchGlobal(true)
            )
        ;

        $dataGridEntity
            ->addMenu(new MenuEntity($this->translator->translate('Upravit'), 'edit'));

        return $dataGridEntity;
    }
}