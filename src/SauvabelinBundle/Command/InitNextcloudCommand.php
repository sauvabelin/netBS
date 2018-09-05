<?php

namespace SauvabelinBundle\Command;

use NetBS\CoreBundle\Utils\StrUtil;
use NextcloudApiWrapper\Wrapper;
use Sabre\DAV\Client;
use SauvabelinBundle\Entity\BSGroupe;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InitNextcloudCommand extends ContainerAwareCommand
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var Wrapper
     */
    private $wrapper;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sauvabelin:init-nextcloud');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io       = new SymfonyStyle($input, $output);
        $this->wrapper  = $this->getContainer()->get('nextcloud.wrapper');
        $io             = $this->io;
        $em             = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->client   = $this->getContainer()->get('webdav.nextcloud_client');

        $this->client->addCurlSetting(CURLOPT_SSL_VERIFYHOST, false);
        $this->client->addCurlSetting(CURLOPT_SSL_VERIFYPEER, false);

        $io->title("Building shared directories");
        //Chargement des dossiers partagés pour les différents groupes
        $types  = [
            "meutes"            => "meute",
            "troupes"           => "troupe",
            "clans"             => "clan",
            "edc"               => "équipe de commandement",
            "équipes internes"  => "équipe interne",
            "associations"      => "association"
        ];

        foreach($types as $path => $name) {

            if(!$this->mkdir($path)) {
                $io->error("Failed creating main directory $path");
                continue;
            }

            $type       = $em->getRepository('NetBSFichierBundle:GroupeType')->findOneBy(array('nom' => $name));
            $groupes    = $em->getRepository('SauvabelinBundle:BSGroupe')->findBy(array('groupeType' => $type));

            foreach($groupes as $groupe) {

                $subPath    = $path . "/" . $groupe->getNom();
                if($this->mkdir($subPath))
                    $this->handleGroup($groupe, $subPath);
                else
                    $io->writeln("[ERROR] Failed creating directory $subPath");
            }
        }
    }

    private function mkdir($path) {

        $this->io->writeln("[INFO] Creating " . StrUtil::removeAccents($path));
        $response   = $this->client->request("MKCOL", $this->encodePath($path));
        $code       = $response['statusCode'];

        if($code === 405)
            $this->io->warning("directory $path already exist");

        return $code === 405 || ($code >= 200 && $code < 230);
    }

    private function handleGroup(BSGroupe $groupe, $path) {

        $this->io->writeln("[INFO] Creating nextcloud share " . $groupe->getNcGroupName() . " and path " . $path);

        $createShare    = $this->wrapper->getSharesClient()->createShare([
            'path'          => $path,
            'shareType'     => 1,
            'shareWith'     => $groupe->getNcGroupName(),
            'publicUpload'  => false,
            'permissions'   => 31
        ]);

        $code = $createShare->getStatusCode();

        if($code < 200 || $code > 230)
            $this->io->error("Failed creating nextcloud share for group " . $groupe->getNcGroupName() .
                " and path " . $path);
    }

    private function encodePath($path) {

        $realPath   = "";
        foreach(explode("/", $path) as $segment)
            $realPath .= rawurlencode($segment) . "/";

        return trim($realPath, "/") . "/";
    }

    private function decodePath($path) {

        $realPath   = "";
        foreach(explode("/", $path) as $segment)
            $realPath .= rawurldecode($segment) . "/";

        return trim($realPath, "/") . "/";
    }

}
