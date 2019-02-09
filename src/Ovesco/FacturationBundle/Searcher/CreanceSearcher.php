<?php

namespace Ovesco\FacturationBundle\Searcher;

use NetBS\CoreBundle\ListModel\Column\HelperColumn;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\CoreBundle\Model\BaseSearcher;
use NetBS\ListBundle\Column\DateTimeColumn;
use NetBS\ListBundle\Column\SimpleColumn;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use Ovesco\FacturationBundle\Entity\Creance;
use Ovesco\FacturationBundle\Form\SearchCreanceType;
use Ovesco\FacturationBundle\Util\CreanceListTrait;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CreanceSearcher extends BaseSearcher
{
    use CreanceListTrait;

    /**
     * Returns the search form type class
     * @return string
     */
    public function getSearchType()
    {
        return SearchCreanceType::class;
    }

    /**
     * Returns an object used to render form, which will contain search data
     * @return object
     */
    public function getSearchObject()
    {
        $creance = new Creance();
        $creance->setRabais(null)->setDate(null);
        return $creance;
    }

    /**
     * Returns the twig template used to render the form. A variable casually named 'form' will be available
     * for you to use
     * @return string
     */
    public function getFormTemplate()
    {
        return "@OvescoFacturation/creance/search_creance.html.twig";
    }

    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn('numero', 'id', SimpleColumn::class)
            ->addColumn('titre', null, XEditableColumn::class, [
                XEditableColumn::TYPE_CLASS => TextType::class,
                XEditableColumn::PROPERTY => 'titre',
            ])
            ->addColumn('Débiteur', 'debiteur', HelperColumn::class)
            ->addColumn('Date de création', 'date', DateTimeColumn::class)
            ->addColumn('Montant', null, XEditableColumn::class, [
                XEditableColumn::TYPE_CLASS => NumberType::class,
                XEditableColumn::PROPERTY => 'montant',
            ])
            ->addColumn('Rabais', null, XEditableColumn::class, [
                XEditableColumn::TYPE_CLASS => NumberType::class,
                XEditableColumn::PROPERTY => 'rabais',
            ])
        ;
    }
}