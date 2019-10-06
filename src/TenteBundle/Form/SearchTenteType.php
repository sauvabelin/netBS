<?php

namespace TenteBundle\Form;

use NetBS\CoreBundle\Form\Type\Select2DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TenteBundle\Entity\Tente;
use TenteBundle\Entity\TenteModel;

class SearchTenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero', TextType::class, ['label' => 'Numéro de tente', 'required' => false])
            ->add('model', Select2DocumentType::class, [
                'label' => 'Modèle de tente',
                'required' => false,
                'choice_label' => 'name',
                'class' => TenteModel::class,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'required' => false,
                'choices' => array_flip(Tente::getStatutChoices()),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tente::class,
        ]);
    }
}