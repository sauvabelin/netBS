<?php

namespace SauvabelinBundle\Form\Search;

use SauvabelinBundle\Model\SearchMembre;
use Symfony\Component\Form\FormBuilderInterface;
use NetBS\FichierBundle\Form\Search\SearchBaseMembreInformationType as base;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchBaseMembreInformationType extends base
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('noAdabs', SearchNoAdabsType::class, ["label" => "Pas à l'ADABS", 'data' => true]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SearchMembre::class
        ));
    }
}
