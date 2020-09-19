<?php

namespace NetBS\SecureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="netbs.secure.login.login")
     */
    public function loginAction()
    {
        $authenticationUtils    = $this->get('security.authentication_utils');

        return $this->render('@NetBSSecure/login/login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ));
    }

    /**
     * @Route("/logout", name="netbs.secure.login.logout")
     */
    public function logoutAction()
    {
    }
}
