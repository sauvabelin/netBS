<?php

namespace TenteBundle\ListModel;

use NetBS\CoreBundle\ListModel\Column\LinkColumn;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\CoreBundle\Utils\Traits\RouterTrait;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TenteBundle\Entity\Tente;

class TenteModelTentesList extends BaseListModel
{
    use EntityManagerTrait, RouterTrait;

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        return $this->entityManager->getRepository('TenteBundle:Tente')
            ->findBy(['model' => $this->getParameter('modelId')]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired('modelId');
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
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return 'tente.tente_model_tentes';
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(\NetBS\ListBundle\Model\ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn('Numero', null, LinkColumn::class, [
                LinkColumn::LABEL => function($t) { return $t->getNumero(); },
                LinkColumn::ROUTE => function($t) { return $this->router->generate('tente.tente.details', ['id' => $t->getId()]); },
            ])
            ->addColumn('Statut', null, XEditableColumn::class, [
                XEditableColumn::TYPE_CLASS => ChoiceType::class,
                XEditableColumn::PROPERTY => 'status',
                XEditableColumn::PARAMS => [
                    'choices' => array_flip(Tente::getStatutChoices()),
                ],
            ]);
    }
}