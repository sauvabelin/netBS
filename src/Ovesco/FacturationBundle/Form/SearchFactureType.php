<?php

namespace Ovesco\FacturationBundle\Form;

use NetBS\CoreBundle\Form\Type\DatepickerType;
use Ovesco\FacturationBundle\Entity\Compte;
use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Form\Type\CountSearchType;
use Ovesco\FacturationBundle\Form\Type\HasBeenPrintedType;
use Ovesco\FacturationBundle\Form\Type\LatestDateType;
use Ovesco\FacturationBundle\Form\Type\CompareType;
use Ovesco\FacturationBundle\Model\SearchFacture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('factureId', CompareType::class, ['label' => 'Numéro de facture', 'property' => 'factureId', 'function' => 'intval'])
            ->add('date', DatepickerType::class, ['label' => 'Date de création', 'required' => false])
            ->add('dateImpression', LatestDateType::class, ['label' => "Date de dernière impression", 'property' => 'impression'])
            ->add('datePaiement', LatestDateType::class, ['label' => 'Date de dernier paiement', 'property' => 'paiement'])
            ->add('isPrinted', HasBeenPrintedType::class, ['label' => "En attente d'impression"])
            ->add('compteToUse', EntityType::class, ['label' => 'Compte', 'required' => false, 'class' => Compte::class])
            ->add('statut', ChoiceType::class, ['label' => 'statut', 'choices' => array_flip([
                Facture::PAYEE      => 'payée',
                Facture::ANNULEE    => 'annulée',
                Facture::OUVERTE    => 'ouverte'
            ])])
            ->add('nombreDeRappels', CountSearchType::class, ['label' => 'Nombre de rappels', 'property' => 'rappels'])
            ->add('nombreDeCreances', CountSearchType::class, ['label' => 'Nombre de créances', 'property' => 'creances'])
            ->add('montant', CompareType::class, ['label' => 'Montant total', 'property' => 'montant', 'function' => 'floatval'])
            ->add('montantPaye', CompareType::class, ['label' => 'Montant payé', 'property' => 'montantPaye', 'function' => 'floatval'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => SearchFacture::class
        ]);
    }
}
