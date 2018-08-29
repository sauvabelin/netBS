<?php

namespace SauvabelinBundle\Form;

use NetBS\CoreBundle\Form\Type\SwitchType;
use SauvabelinBundle\Entity\News;
use SauvabelinBundle\Entity\NewsChannel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SauvabelinBundle\Form\Type\NewsChannelType as NC;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("channel", NC::class, [
                'label'         => 'Channel',
                'class'         => NewsChannel::class,
                'choice_label'  => "nom"
            ])
            ->add('importante', SwitchType::class, ['label' => "Très importante"])
            ->add("titre", TextType::class, ['label' => "Titre"])
            ->add("contenu", TextareaType::class, ['label' => "Contenu"])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => News::class
        ));
    }
}
