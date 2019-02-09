<?php

namespace Ovesco\FacturationBundle\Form;

use NetBS\CoreBundle\Form\Type\DatepickerType;
use Ovesco\FacturationBundle\Entity\Compte;
use Ovesco\FacturationBundle\Entity\Facture;
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
            ->add('date', DatepickerType::class, ['label' => 'Date de création', 'required' => false])
            ->add('compteToUse', EntityType::class, ['label' => 'Compte à utiliser', 'class' => Compte::class])
            ->add('statut', ChoiceType::class, ['label' => 'statut', 'choices' => [
                Facture::PAYEE      => 'payée',
                Facture::ANNULEE    => 'annulée',
                Facture::OUVERTE    => 'ouverte'
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => Facture::class
        ]);
    }
}
