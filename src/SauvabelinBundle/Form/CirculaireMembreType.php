<?php

namespace SauvabelinBundle\Form;

use NetBS\CoreBundle\Form\Type\AjaxSelect2DocumentType;
use NetBS\CoreBundle\Form\Type\DateMaskType;
use NetBS\CoreBundle\Form\Type\DatepickerType;
use NetBS\CoreBundle\Form\Type\MaskType;
use NetBS\CoreBundle\Form\Type\Select2DocumentType;
use NetBS\CoreBundle\Form\Type\SexeType;
use NetBS\CoreBundle\Form\Type\TelephoneMaskType;
use NetBS\FichierBundle\Entity\Fonction;
use NetBS\FichierBundle\Entity\Geniteur;
use NetBS\FichierBundle\Entity\Groupe;
use SauvabelinBundle\Entity\BSGroupe;
use SauvabelinBundle\Model\CirculaireMembre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CirculaireMembreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('familleId', HiddenType::class)
            ->add('numero', NumberType::class, array('label' => "Numéro BS", 'required' => false))
            ->add('prenom', TextType::class, array('label' => 'Prénom'))
            ->add('nom', TextType::class, array('label' => 'Nom de famille'))
            ->add('sexe', SexeType::class, array('label' => 'Sexe'))
            ->add('naissance', DateMaskType::class, array('label' => 'Date de naissance'))
            ->add('adresse', TextType::class, array('label' => "Adresse", 'required' => false))
            ->add('npa', NumberType::class, array('label' => "NPA", 'required' => false))
            ->add('localite', TextType::class, array('label' => 'Localité', 'required' => false))
            ->add('email', EmailType::class, array('label' => 'Email', 'required' => false))
            ->add('telephone', TelephoneMaskType::class, array('label' => 'Téléphone', 'required' => false))
            ->add('natel', TelephoneMaskType::class, array('label' => 'Natel', 'required' => false))
            ->add('fonction', Select2DocumentType::class, array(
                'class'         => Fonction::class,
                'choice_label'  => 'nom',
                'label'         => 'Fonction'
            ))
            ->add('groupe', AjaxSelect2DocumentType::class, array(
                'class'         => BSGroupe::class,
                'label'         => 'Unité'
            ))
            ->add('r1statut', ChoiceType::class, [
                'label'     => 'Statut',
                'choices'   => array_flip(Geniteur::getStatutChoices())
            ])
            ->add('r1sexe', SexeType::class, array('label' => 'Sexe', 'required' => false))
            ->add('r1nom', TextType::class, array('label' => 'Nom', 'required' => false))
            ->add('r1prenom', TextType::class, array('label' => 'Prénom', 'required' => false))
            ->add('r1adresse', TextType::class, array('label' => 'Adresse', 'required' => false))
            ->add('r1npa', NumberType::class, array('label' => 'NPA', 'required' => false))
            ->add('r1localite', TextType::class, array('label' => 'Localité', 'required' => false))
            ->add('r1telephone', TelephoneMaskType::class, array('label' => 'Téléphone', 'required' => false))
            ->add('r1email', EmailType::class, array('label' => 'Email', 'required' => false))
            ->add('r1profession', TextType::class, array('label' => 'Profession', 'required' => false))

            ->add('r2statut', ChoiceType::class, [
                'label'     => 'Statut',
                'choices'   => array_flip(Geniteur::getStatutChoices())
            ])
            ->add('r2sexe', SexeType::class, array('label' => 'Sexe', 'required' => false))
            ->add('r2nom', TextType::class, array('label' => 'Nom', 'required' => false))
            ->add('r2prenom', TextType::class, array('label' => 'Prénom', 'required' => false))
            ->add('r2adresse', TextType::class, array('label' => 'Adresse', 'required' => false))
            ->add('r2npa', NumberType::class, array('label' => 'NPA', 'required' => false))
            ->add('r2localite', TextType::class, array('label' => 'Localité', 'required' => false))
            ->add('r2telephone', TelephoneMaskType::class, array('label' => 'Téléphone', 'required' => false))
            ->add('r2email', EmailType::class, array('label' => 'Email', 'required' => false))
            ->add('r2profession', TextType::class, array('label' => 'Profession', 'required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CirculaireMembre::class
        ));
    }
}
