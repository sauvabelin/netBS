<?php

namespace Ovesco\FacturationBundle\Form\Export;

use NetBS\CoreBundle\Form\Type\SwitchType;
use Ovesco\FacturationBundle\Exporter\Config\CSVPaiementConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CSVPaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creances', SwitchType::class, array('label' => "Creances de la facture"))
            ->add('compte', SwitchType::class, array('label' => "Compte de destination"))
            ->add('montantFacture', SwitchType::class, array('label' => "Montant facture"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', CSVPaiementConfig::class);
    }
}
