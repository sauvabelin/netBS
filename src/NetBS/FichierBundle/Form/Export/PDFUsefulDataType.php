<?php

namespace NetBS\FichierBundle\Form\Export;

use NetBS\CoreBundle\Form\Type\SwitchType;
use NetBS\FichierBundle\Exporter\Config\PDFListMembresConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PDFUsefulDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cravateBleue', SwitchType::class, array('label' => 'Date cravate bleue', 'required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', PDFListMembresConfig::class);
    }
}