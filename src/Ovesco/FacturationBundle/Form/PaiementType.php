<?php

namespace Ovesco\FacturationBundle\Form;

use NetBS\FichierBundle\Utils\Form\RemarquesUtils;
use Ovesco\FacturationBundle\Entity\Paiement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('montant', NumberType::class, ['label' => 'montant'])
            ->add('date', DateType::class, ['label' => 'date'])
            ->add('facture', EntityType::class, ['label' => 'facture'])
            ->add('compte', EntityType::class, ['label' => 'Compte utilisé'])
        ;

        RemarquesUtils::addRemarquesField($builder);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => Paiement::class
        ]);
    }
}
