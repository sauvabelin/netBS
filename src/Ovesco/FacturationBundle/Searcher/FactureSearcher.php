<?php

namespace Ovesco\FacturationBundle\Searcher;

use NetBS\CoreBundle\ListModel\Action\ModalAction;
use NetBS\CoreBundle\ListModel\Action\RemoveAction;
use NetBS\CoreBundle\ListModel\ActionItem;
use NetBS\CoreBundle\ListModel\Column\ActionColumn;
use NetBS\CoreBundle\ListModel\Column\HelperColumn;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\CoreBundle\Model\BaseSearcher;
use NetBS\CoreBundle\Utils\Traits\RouterTrait;
use NetBS\ListBundle\Column\DateTimeColumn;
use NetBS\ListBundle\Column\SimpleColumn;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Form\SearchFactureType;
use Ovesco\FacturationBundle\Model\SearchFacture;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FactureSearcher extends BaseSearcher
{
    use RouterTrait;

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
            ->addColumn('numero', 'factureId', SimpleColumn::class)
            ->addColumn('Débiteur', 'debiteur', HelperColumn::class)
            ->addColumn('statut', null, XEditableColumn::class, [
                XEditableColumn::TYPE_CLASS => ChoiceType::class,
                XEditableColumn::PROPERTY => 'statut',
                XEditableColumn::PARAMS => ['choices' => Facture::getStatutChoices()]
            ])
            ->addColumn('Date de création', 'date', DateTimeColumn::class)
            ->addColumn("Dernière impression", 'latestImpression', DateTimeColumn::class)
            ->addColumn('Montant', 'montant', SimpleColumn::class)
            ->addColumn('Montant payé', 'montantPaye', SimpleColumn::class)
            ->addColumn('', null, ActionColumn::class, [
                ActionColumn::ACTIONS_KEY => [
                    new ActionItem(ModalAction::class, [
                        ModalAction::TEXT => '+ paiement',
                        ModalAction::ROUTE => function(Facture $facture) {
                            return $this->router->generate('ovesco.facturation.paiement.modal_add', ['id' => $facture->getId()]);
                        }
                    ]),
                    new ActionItem(ModalAction::class, [
                        ModalAction::ICON => 'fas fa-expand',
                        ModalAction::ROUTE => function(Facture $facture) {
                            return $this->router->generate('ovesco.facturation.facture_modal', ['id' => $facture->getId()]);
                        }
                    ]),
                    new ActionItem(RemoveAction::class)
                ]
            ])
        ;
    }
}
