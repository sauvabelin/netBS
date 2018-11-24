<?php

namespace NetBS\FichierBundle\Form\Export;

use NetBS\CoreBundle\Form\PDFConfig\FPDFType;
use NetBS\CoreBundle\Form\Type\SwitchType;
use NetBS\FichierBundle\Exporter\Config\EtiquettesConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtiquettesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('colonnes', NumberType::class, array('label' => "Colonnes"))
            ->add('lignes', NumberType::class, array('label' => "Lignes"))
            ->add('taillePolice', NumberType::class, array('label' => "Taille de la police"))
            ->add('margeInterieureVerticale', NumberType::class, array('label' => "Marge interne verticale"))
            ->add('margeInterieureHorizontale', NumberType::class, array('label' => "Marge interne horizontale"))
            ->add('margeInferieure', NumberType::class, array('label' => "Marge en bas"))
            ->add('margeDroite', NumberType::class, array('label' => "Marge à droite"))
            ->add('FPDFConfig', FPDFType::class, array('label' => 'Configuration générale'))
            ->add('showInfoPage', SwitchType::class, array('label' => "Afficher la page d'info"))
            ->add('mergeFamilles', SwitchType::class, array('label' => 'Fusionner les familles'))
            ->add('title', TextType::class, array('label' => 'Titre étiquette enfant', 'required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', EtiquettesConfig::class);
    }
}