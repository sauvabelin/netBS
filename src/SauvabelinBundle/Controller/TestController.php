<?php

namespace SauvabelinBundle\Controller;

use SauvabelinBundle\Entity\BSGroupe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class TestController extends Controller
{
    /**
     * @Route("/grp/test")
     */
    public function testGroupe() {

    }

    /**
     * @Route("/test/mail")
     */
    public function testMail() {

        $mailer = $this->get('netbs.mailer');

        $mailer->send('@Sauvabelin/mailer/account_created.piece.twig', 'Compte créé', 'mashallah@gmail.com', [
            'username'  => 'guillaume',
            'password'  => 'yoloswag22'
        ]);
        return new Response();
    }


    /**
     * @Route("/test")
     */
    public function test() {

        $manager    = $this->get('sauvabelin.isp_config_manager');
        //$result     = $manager->createMailbox(20, 'hamdoulilah', 'abcd.efghij', 'yoloswag22', 'abcd.efghij@sauvabelin.ch');
        $result     = $manager->getMailbox('yolo.swag@sauvabelin.ch');

        dump($result);

        return new Response();
    }
}
