<?php

namespace TenteBundle\Searcher;

use NetBS\CoreBundle\ListModel\Action\IconAction;
use NetBS\CoreBundle\ListModel\Column\ActionColumn;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\CoreBundle\Model\BaseSearcher;
use NetBS\CoreBundle\Utils\Traits\RouterTrait;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use TenteBundle\Entity\Tente;
use TenteBundle\Form\SearchTenteType;

class TenteSearcher extends BaseSearcher
{
    use RouterTrait;

    /**
     * Returns the search form type class
     * @return string
     */
    public function getSearchType()
    {
        return SearchTenteType::class;
    }

    /**
     * Returns an object used to render form, which will contain search data
     * @return object
     */
    public function getSearchObject()
    {
        return new Tente();
    }

    /**
     * Returns the twig template used to render the form. A variable casually named 'form' will be available
     * for you to use
     * @return string
     */
    public function getFormTemplate()
    {
        return '@Tente/tente/search_tente.html.twig';
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return Tente::class;
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(\NetBS\ListBundle\Model\ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn('Numero', null, XEditableColumn::class, [
                XEditableColumn::PROPERTY => 'numero',
                XEditableColumn::TYPE_CLASS => TextType::class,
            ])
            ->addColumn('Statut', null, XEditableColumn::class, [
                XEditableColumn::TYPE_CLASS => ChoiceType::class,
                XEditableColumn::PROPERTY => 'status',
                XEditableColumn::PARAMS => [
                    'choices' => array_flip(Tente::getStatutChoices()),
                ],
            ])
            ->addColumn('Actions', null, ActionColumn::class, [
                ActionColumn::ACTIONS_KEY => [
                    IconAction::class => [
                        IconAction::ROUTE => function($t) { return $this->router->generate('tente.tente.details', ['id' => $t->getId()]); },
                        IconAction::ICON => 'fas fa-eye'
                    ]
                ],
            ]);
    }
}