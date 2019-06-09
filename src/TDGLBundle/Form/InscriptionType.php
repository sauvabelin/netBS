<?php

namespace TDGLBundle\Form;

use NetBS\CoreBundle\Form\Type\DateMaskType;
use NetBS\CoreBundle\Form\Type\Select2DocumentType;
use NetBS\CoreBundle\Form\Type\SexeType;
use NetBS\CoreBundle\Form\Type\TelephoneMaskType;
use NetBS\FichierBundle\Entity\Fonction;
use NetBS\FichierBundle\Entity\Groupe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TDGLBundle\Model\Inscription;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('familleId', HiddenType::class)
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('prenom', TextType::class, ['label' => 'Prénom'])
            ->add('sexe', SexeType::class, ['sexe' => 'Prénom'])
            ->add('naissance', DateMaskType::class, ['label' => 'Date de naissance'])
            ->add('adresse', TextType::class, ['label' => 'Adresse'])
            ->add('npa', TextType::class, ['label' => 'NPA'])
            ->add('localite', TextType::class, ['label' => 'Localité'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('telephone', TelephoneMaskType::class, ['label' => 'Téléphone'])
            ->add('professionsParents', TextType::class, ['label' => 'Professions des parents (séparer par une virgule)', 'required' => true])
            ->add('unite', Select2DocumentType::class, array(
                'class' => Groupe::class,
                'label' => 'Unité'
            ))
            ->add('fonction', Select2DocumentType::class, array(
                'class' => Fonction::class,
                'label' => 'Fonction'
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Inscription::class
        ]);
    }
}
