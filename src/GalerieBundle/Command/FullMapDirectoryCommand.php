<?php

namespace GalerieBundle\Command;

use GalerieBundle\Exceptions\MappingException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FullMapDirectoryCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('netbs:galerie:map-directory')
            ->addArgument("path", InputArgument::REQUIRED)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io     = new SymfonyStyle($input, $output);
        $path   = $input->getArgument("path");
        $mapper = $this->getContainer()->get('galerie.mapper');
        $logger = $this->getContainer()->get('netbs.logger');

        try {
            $mapper->fullMapDirectory($path, $io);
        } catch (MappingException $e) {
            $logger->logUsername("admin", $e->getLevel(), $e->getMessage());
        }
    }
}
