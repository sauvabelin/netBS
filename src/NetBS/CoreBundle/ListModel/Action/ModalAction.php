<?php

namespace NetBS\CoreBundle\ListModel\Action;

use NetBS\CoreBundle\ListModel\Column\LinkColumn;
use NetBS\CoreBundle\Twig\Extension\AssetsExtension;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\Routing\Router;

class ModalAction extends IconAction
{
    public function __construct(AssetExtension $asset, AssetsExtension $registrer, Router $router)
    {
        parent::__construct($router);
        $registrer->registerJs($asset->getAssetUrl('bundles/netbscore/js/modal.js'));
    }

    public function render($item, $params = [])
    {
        $route  = is_string($params[LinkColumn::ROUTE])
            ? $params[LinkColumn::ROUTE]
            : ($params[LinkColumn::ROUTE])($item);

        $params[LinkAction::ROUTE]  = "#";
        $params[LinkAction::TAG]    = 'btn';
        $params[LinkAction::ATTRS]  = $params[LinkAction::ATTRS] . " data-modal data-modal-url='$route'";

        return parent::render($item, $params);
    }
}