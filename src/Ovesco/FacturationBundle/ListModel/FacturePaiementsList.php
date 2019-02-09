<?php

namespace Ovesco\FacturationBundle\ListModel;

use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\ListBundle\Column\DateTimeColumn;
use NetBS\ListBundle\Column\SimpleColumn;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Entity\Paiement;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacturePaiementsList extends BaseListModel
{
    use EntityManagerTrait;

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        $facture = $this->getParameter('facture');
        return $facture instanceof Facture
            ? $facture->getPaiements()
            : $this->entityManager->getRepository('OvescoFacturationBundle:Paiement')
                ->findBy(['facture' => $facture]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->isRequired('facture');
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return Paiement::class;
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return 'ovesco.facturation.facture_paiements';
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn('Date d\'enregistrement', 'date', DateTimeColumn::class)
            ->addColumn('Montant', 'montant', SimpleColumn::class)
            ->addColumn('Compte credité', 'compte.ccp', SimpleColumn::class)
        ;
    }
}