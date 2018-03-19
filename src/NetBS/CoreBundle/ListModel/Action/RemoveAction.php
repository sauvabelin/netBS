<?php

namespace NetBS\CoreBundle\ListModel\Action;

use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Routing\Router;

class RemoveAction implements ActionInterface
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router   = $router;
    }

    public function render($item)
    {
        $path   = $this->router->generate('netbs.core.action.remove_item', [
            'itemId'    => $item->getId(),
            'itemClass' => base64_encode(ClassUtils::getRealClass(get_class($item)))
        ]);

        return "<a onclick='return confirm(\"Etes-vous sûr ?\");' class='btn btn-xs btn-danger' href='$path' data-toggle='tooltip' data-placement='top' title='Supprimer cet élément'>
                    <i class='fas fa-times fa-xs'></i>
                </a>";
    }
}