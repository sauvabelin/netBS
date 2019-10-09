<?php

namespace TenteBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TenteBundle\Entity\TenteModel;

class TenteModelType extends TenteModelNameType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('form', HiddenType::class)
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