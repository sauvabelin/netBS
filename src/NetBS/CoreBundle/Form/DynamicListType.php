<?php

namespace NetBS\CoreBundle\Form;

use NetBS\CoreBundle\Entity\DynamicList;
use NetBS\CoreBundle\Service\DynamicListManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DynamicListType extends AbstractType
{
    protected $dlm;

    public function __construct(DynamicListManager $manager)
    {
        $this->dlm  = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'Nom de la liste'))
            ->add('itemsClass', ChoiceType::class, array(
                'label'     => 'Éléments contenus',
                'choices'   => $this->dlm->getManagedClasses()
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            /** @var DynamicList $list */
            $list   = $event->getData();
            if(empty($list->getItemsClass()))
                $event->getForm()->get('itemsClass')->getConfig()->getOptions()['disabled'] = true; //TODO make this work, supposed to hide itemsClass if created from a list model button
        });
    }
}
