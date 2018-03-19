<?php

namespace NetBS\CoreBundle\Twig\Extension;

use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormView;

class XEditableExtension extends \Twig_Extension
{
    /**
     * @var FormFactory
     */
    protected $form;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct(FormFactory $factory, \Twig_Environment $twig)
    {
        $this->form = $factory;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'xeditable';
    }

    public function getFunctions() {

        return [
            new \Twig_SimpleFunction('xeditable', array($this, 'toXeditable')),
            new \Twig_SimpleFunction('get_class', array($this, 'getClass'))
        ];
    }

    public function getFilters() {

        return [
            new \Twig_SimpleFilter('xEditableChoices', array($this, 'toChoices'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('xEditableSelect2', array($this, 'toSelect2'), array('is_safe' => array('html')))
        ];
    }


    public function toXeditable($object, $field, $type, $typeParams = []) {

        $form = $this->form->createBuilder(FormType::class, $object, array('csrf_protection' => false));
        $form->add($field, $type, $typeParams);

        return $this->twig->render('@NetBSCore/column/xeditable.column.twig', array(
            'form'  => $form->getForm()->createView()
        ));
    }

    /**
     * @param FormView $object
     * @return string
     */
    public function getClass($object) {

        return ClassUtils::getRealClass(get_class($object));
    }

    public function toChoices(array $choices) {

        $return = [];

        /** @var ChoiceView $option */
        foreach($choices as $option)
            $return[] = (object)['value' => $option->value, 'text' => $option->label];

        return json_encode($return);
    }

    public function toSelect2(array $choices) {

        $return = [];

        /** @var ChoiceView $option */
        foreach($choices as $option)
            $return[] = (object)['id' => $option->value, 'text' => $option->label];

        return json_encode($return);
    }

}
