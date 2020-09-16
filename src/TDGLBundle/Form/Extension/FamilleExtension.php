<?php

namespace TDGLBundle\Form\Extension;

use NetBS\FichierBundle\Form\FamilleType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FamilleExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('professionsParents', TextType::class, array('label' => 'Professions des parents'));
    }

    public function getExtendedType()
    {
        return FamilleType::class;
    }
}
