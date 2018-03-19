<?php

namespace NetBS\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/", name="netbs.core.home.dashboard")
     */
    public function indexAction()
    {
        $layout     = $this->get('netbs.core.block.layout');
        $config     = $layout::configurator();

        return $layout->renderResponse('netbs', $config, [
            'title' => "Accueil"
        ]);
    }
}
