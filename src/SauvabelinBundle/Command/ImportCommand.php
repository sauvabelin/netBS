<?php

namespace SauvabelinBundle\Command;

use SauvabelinBundle\Import\Importator;
use SauvabelinBundle\Import\MembresMerger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sauvabelin:import:wng');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io                 = new SymfonyStyle($input, $output);
        $merger             = new MembresMerger();
        $importator         = new Importator();

        $io->writeln("Loading remote data in memory");
        $WNGMembres         = $importator->loadMembres();
        $WNGAttributions    = $importator->loadAttributions();
        $WNGDistinctions    = $importator->loadDistinctions();
        $WNGFonctions       = $importator->loadFonctions();
        $WNGODs             = $importator->loadObtentionsDistinctions();
        $WNGUnites          = $importator->loadGroupes();

        $io->table(
            ["membres", "attributions", "obtentions distinction"],
            [[count($WNGMembres), count($WNGAttributions), count($WNGODs)]]
        );

        $io->writeln("Map attributions");

        foreach($WNGAttributions as $attribution) {

            if(isset($WNGUnites[$attribution->idUnite]))
                $attribution->WNGUnite      = $WNGUnites[$attribution->idUnite];

            if(isset($WNGFonctions[$attribution->idFonction]))
                $attribution->WNGFonction   = $WNGFonctions[$attribution->idFonction];

            if(isset($WNGMembres[$attribution->idMembre]))
                $WNGMembres[$attribution->idMembre]->WNGAttributions[] = $attribution;
        }


        $io->writeln("Map distinctions");

        foreach($WNGODs as $obtentionDistinction) {

            if(isset($WNGDistinctions[$obtentionDistinction->idDistinction]))
                $obtentionDistinction->WNGDistinction   = $WNGDistinctions[$obtentionDistinction->idDistinction];

            if(isset($WNGMembres[$obtentionDistinction->idMembre]))
                $WNGMembres[$obtentionDistinction->idMembre]->WNGObtentionsDistinctions[] = $obtentionDistinction;
        }


        $io->writeln("Merge members");
        $pool   = $merger->generatePool($WNGMembres);
        $io->writeln("DAYUM");
    }
}
