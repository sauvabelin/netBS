<?php

namespace Ovesco\FacturationBundle\Searcher;

use NetBS\CoreBundle\ListModel\Column\HelperColumn;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\CoreBundle\Model\BaseSearcher;
use NetBS\ListBundle\Column\DateTimeColumn;
use NetBS\ListBundle\Column\SimpleColumn;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Form\SearchFactureType;
use Ovesco\FacturationBundle\Model\SearchFacture;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FactureSearcher extends BaseSearcher
{
    /**
     * Returns the search form type class
     * @return string
     */
    public function getSearchType()
    {
        return SearchFactureType::class;
    }

    /**
     * Returns an object used to render form, which will contain search data
     * @return object
     */
    public function getSearchObject()
    {
        return new SearchFacture();
    }

    /**
     * Returns the twig template used to render the form. A variable casually named 'form' will be available
     * for you to use
     * @return string
     */
    public function getFormTemplate()
    {
        return "@OvescoFacturation/facture/search_facture.html.twig";
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return Facture::class;
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn('numero', null, HelperColumn::class)
            ->addColumn('Débiteur', 'debiteur', HelperColumn::class)
            ->addColumn('statut', null, XEditableColumn::class, [
                XEditableColumn::TYPE_CLASS => ChoiceType::class,
                XEditableColumn::PROPERTY => 'statut',
                XEditableColumn::PARAMS => ['choices' => Facture::getStatutChoices()]
            ])
            ->addColumn('Date de création', 'date', DateTimeColumn::class)
            ->addColumn('Montant', 'montant', SimpleColumn::class)
            ->addColumn('Reste à payer', 'montantEncoreDu', SimpleColumn::class)
            ->addColumn('Compte', 'compteToUse.ccp', SimpleColumn::class)
        ;
    }
}
