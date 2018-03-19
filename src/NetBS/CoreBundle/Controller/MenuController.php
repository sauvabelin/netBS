<?php

namespace NetBS\CoreBundle\Controller;

use NetBS\CoreBundle\Event\ExtendMainMenuEvent;
use NetBS\CoreBundle\Menu\MainMenu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller
{
    /**
     * @Route("/menu/render-main", name="netbs.core.menu.render_main")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderMainMenuAction()
    {
        $menu       = new MainMenu();
        $dispatcher = $this->get('event_dispatcher');

        $dispatcher->dispatch(ExtendMainMenuEvent::KEY, new ExtendMainMenuEvent($menu));

        return $this->render('@NetBSCore/partial/menubar.partial.twig', array(
            'route' => $this->get('request_stack')->getParentRequest()->get('_route'),
            'menu'  => $menu
        ));
    }
}
