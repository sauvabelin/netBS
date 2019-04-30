<?php

namespace Ovesco\GalerieBundle\Command;

use Ovesco\GalerieBundle\Entity\NewDirectory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewDirectoryCreatedCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ovesco:galerie:new-directory')
            ->addArgument('type', InputArgument::REQUIRED)
            ->addArgument('path', InputArgument::REQUIRED)
            ->addArgument('etag', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path   = $input->getArgument('path');
        $etag   = $input->getArgument('etag');
        $type   = $input->getArgument('type');

        $em     = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $repo   = $em->getRepository('OvescoGalerieBundle:NewDirectory');
        $log    = $repo->findOneBy(array('etag' => $etag));

        dump($type, $path, $etag);

        file_put_contents(__DIR__ . "/" . time() . '.txt', json_encode([
            'type'  => $type,
            'path'  => $path,
            'etag'  => $etag
        ]));

        return;

        switch ($type) {
            case 'create':
                if ($log) $log->setPath($path);
                else $em->persist(new NewDirectory($path, $etag));
                break;
            case 'move':
                $log->setPath($path);
                break;
            case 'delete':
                if ($log) $em->remove($log);
                break;
            default:
                break;
        }

        $em->flush();
    }
}
