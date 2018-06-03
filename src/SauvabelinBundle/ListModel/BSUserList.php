<?php

namespace SauvabelinBundle\ListModel;

use NetBS\CoreBundle\Form\Type\SwitchType;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use NetBS\SecureBundle\ListModel\UsersList;

class BSUserList extends UsersList
{
    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        parent::configureColumns($configuration);

        $configuration
            ->addColumn('Nextcloud', null, XEditableColumn::class, [
            XEditableColumn::PROPERTY       => 'nextcloudAccount',
            XEditableColumn::TYPE_CLASS     => SwitchType::class
            ])
            ->addColumn('Wiki', null, XEditableColumn::class, [
                XEditableColumn::PROPERTY   => 'wikiAccount',
                XEditableColumn::TYPE_CLASS => SwitchType::class
            ])
        ;
    }
}