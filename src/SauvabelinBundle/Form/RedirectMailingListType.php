<?php

namespace SauvabelinBundle\Form;

use SauvabelinBundle\Entity\RedirectMailingList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RedirectMailingListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fromAdresse', EmailType::class, ['label' => "Adresse email source"])
            ->add('toAdresses', TextareaType::class, ['label' => "Adresse emails d'arrivée (séparées par une ',')"])
            ->add('description', TextareaType::class, ['label' => 'Description'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => RedirectMailingList::class
        ));
    }
}
