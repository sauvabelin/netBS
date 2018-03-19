<?php

namespace SauvabelinBundle\ListModel;

use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\ListBundle\Column\SimpleColumn;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use SauvabelinBundle\Model\MailingList;

class DisplayableMailingListsList extends BaseListModel
{
    use EntityManagerTrait;

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        return array_merge(
            $this->entityManager->getRepository('SauvabelinBundle:RedirectMailingList')->findAll(),
            $this->entityManager->getRepository('SauvabelinBundle:RuleMailingList')->findAll()
        );
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return MailingList::class;
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return 'bs.displayable_mailing_lists';
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn('Adresse e-mail', 'fromAdresse', SimpleColumn::class)
            ->addColumn('Description', 'description', SimpleColumn::class)
        ;
    }
}