<?php

namespace SauvabelinBundle\Form;

use SauvabelinBundle\Entity\News;
use SauvabelinBundle\Entity\NewsChannel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("channel", EntityType::class, [
                'label'         => 'Channel',
                'class'         => NewsChannel::class,
                'choice_label'  => "nom"
            ])
            ->add("titre", TextType::class, ['label' => "Titre"])
            ->add("contenu", TextareaType::class, ['label' => "Contenu"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => News::class
        ));
    }
}
