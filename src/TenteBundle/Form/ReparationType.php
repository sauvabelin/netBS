<?php

namespace TenteBundle\Form;

use NetBS\CoreBundle\Form\Type\DatepickerType;
use NetBS\FichierBundle\Utils\Form\RemarquesUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TenteBundle\Entity\Reparation;

class ReparationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('parties', CollectionType::class, [
            'label' => '',
            'allow_add' => false,
            'allow_delete' => false,
            'entry_type' => ReparationPartieType::class,
        ])
            ->add('envoyee', DatepickerType::class, ['label' => "Date d'envoi"])
            ->add('recue', DatepickerType::class, ['label' => "Date de réception", 'required' => false])
            ->add('status', ChoiceType::class, ['label' => 'Statut', 'choices' => array_flip(Reparation::getStatusChoices())]);
        RemarquesUtils::addRemarquesField($builder);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Reparation::class]);
    }
}
