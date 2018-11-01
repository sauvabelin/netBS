<?php

namespace Ovesco\FacturationBundle\Form;

use Doctrine\DBAL\Types\TextType;
use Ovesco\FacturationBundle\Entity\Creance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre de la créance'])
            ->add('montant', NumberType::class, ['label' => 'Montant'])
            ->add('rabais', NumberType::class, ['label' => 'Rabais (en francs)'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Creance::class);
    }
}
