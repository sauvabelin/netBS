<?php

namespace TenteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TenteBundle\Entity\Tente;

class TenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('numero', TextType::class, ['label' => 'Numéro'])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => array_flip(Tente::getStatutChoices()),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tente::class
        ]);
    }
}
