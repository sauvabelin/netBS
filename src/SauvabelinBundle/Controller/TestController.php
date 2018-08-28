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

        dump(Yaml::parse(file_get_contents(__DIR__ . "/../Resources/structure/groups.yml")));

        /*
        $type   = $this->get('doctrine.orm.entity_manager')->getRepository('NetBSFichierBundle:GroupeType')->findOneBy(array('nom' => 'patrouille'));
        $nc     = $this->get('sauvabelin.nextcloud.group_manager');
        $groupe = new BSGroupe();
        $groupe->setNom('Test groupe ' . date('H:i:s'));
        $groupe->setGroupeType($type);
        $groupe->updateNCGroupName();

        $r = $nc->createNCGroup($groupe);

        dump($r->getMessage(), $r->getStatus(), $r->getStatusCode());
        */

        return new Response();
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
