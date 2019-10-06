<?php

namespace TenteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TenteBundle\Entity\TenteModel;

class TenteModelType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['label' => 'Nom du modèle'])
            ->add('form', HiddenType::class)
            ->add('drawingParts', CollectionType::class, [
                'entry_type'    => DrawingPartType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TenteModel::class,
        ]);
    }
}