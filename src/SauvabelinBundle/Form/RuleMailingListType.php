<?php

namespace SauvabelinBundle\Form;

use SauvabelinBundle\Entity\RuleMailingList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RuleMailingListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fromAdresse', EmailType::class, ['label' => "Adresse email source"])
            ->add('elRule', TextareaType::class, ['label' => 'Règle expression language'])
            ->add('description', TextareaType::class, ['label' => 'Description'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => RuleMailingList::class
        ));
    }
}
