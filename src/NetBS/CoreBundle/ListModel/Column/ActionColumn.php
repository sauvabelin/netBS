<?php

namespace NetBS\CoreBundle\ListModel\Column;

use NetBS\CoreBundle\ListModel\Action\ActionInterface;
use NetBS\ListBundle\Column\BaseColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionColumn extends BaseColumn
{
    const ACTIONS_KEY   = 'actions';

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault(self::ACTIONS_KEY, []);
    }

    /**
     * Return content related to the given object with the given params
     * @param object $item
     * @param array $params
     * @return string
     */
    public function getContent($item, array $params = [])
    {
        /** @var ActionInterface[] $actions */
        $actions    = $params[self::ACTIONS_KEY];
        $html       = '';

        foreach($actions as $action)
            $html .= $action->render($item) . " ";

        return $html;
    }
}