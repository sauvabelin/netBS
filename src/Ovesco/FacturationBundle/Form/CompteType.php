<?php

namespace Ovesco\FacturationBundle\Form;

use NetBS\FichierBundle\Utils\Form\RemarquesUtils;
use Ovesco\FacturationBundle\Entity\Compte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ccp', TextType::class, ['label' => 'CCP'])
            ->add('iban', TextType::class, ['label' => 'IBAN'])
            ->add('line1', TextType::class, ['label' => "Première ligne d'adresse", 'required' => false])
            ->add('line2', TextType::class, ['label' => "Seconde ligne d'adresse", 'required' => false])
            ->add('line3', TextType::class, ['label' => "Troisième ligne d'adresse", 'required' => false])
            ->add('line4', TextType::class, ['label' => "Quatrième ligne d'adresse", 'required' => false])
        ;

        RemarquesUtils::addRemarquesField($builder);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => Compte::class
        ]);
    }
}
