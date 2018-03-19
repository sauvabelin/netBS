<?php

namespace NetBS\CoreBundle\ListModel\Column;

use NetBS\ListBundle\Column\BaseColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;

class RemoveFromDynamicColumn extends BaseColumn
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router   = $router;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('listId');
    }

    /**
     * Return content related to the given object with the given params
     * @param object $item
     * @param array $params
     * @return string
     */
    public function getContent($item, array $params = [])
    {
        $path = $this->router->generate('netbs.core.dynamics_list.remove_item', array('id' => $params['listId'], 'itemId' => $item->getId()));
        return "<a href='$path' class='btn btn-xs btn-danger' data-toggle='tooltip' title='Retirer de la liste'><i class='fas fa-sm fa-times'></i></a>";
    }
}