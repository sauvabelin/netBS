<?php

namespace NetBS\CoreBundle\ListModel\Column;

use NetBS\CoreBundle\ListModel\Action\ActionInterface;
use NetBS\CoreBundle\Service\ListActionsManager;
use NetBS\ListBundle\Column\BaseColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionColumn extends BaseColumn
{
    const ACTIONS_KEY   = 'actions';

    private $manager;

    public function __construct(ListActionsManager $manager)
    {
        $this->manager  = $manager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault(self::ACTIONS_KEY, [])
            ->setDefault(BaseColumn::SORTABLE, false);
    }

    /**
     * Return content related to the given object with the given params
     * @param object $item
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function getContent($item, array $params = [])
    {
        /** @var ActionInterface[] $actions */
        $actions    = $params[self::ACTIONS_KEY];
        $html       = '';

        foreach($params[self::ACTIONS_KEY] as $key  => $params) {

            $class      = is_array($params) ? $key : $params;
            $params     = is_array($params) ? $params : [];
            $action     = $this->manager->getAction($class);
            $options    = new OptionsResolver();

            $action->configureOptions($options);
            $data       = $options->resolve($params);

            $html  .= $action->render($item, $data) . " ";
        };

        return $html;
    }
}