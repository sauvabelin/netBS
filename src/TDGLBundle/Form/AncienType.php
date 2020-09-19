<?php

namespace TDGLBundle\Form;

use NetBS\CoreBundle\Form\Type\SwitchType;
use Symfony\Component\Form\AbstractType;

class AncienType extends AbstractType
{
    public function getParent()
    {
        return SwitchType::class;
    }
}
