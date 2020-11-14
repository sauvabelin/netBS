<?php

namespace NetBS\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DevStatusCommand extends ContainerAwareCommand
{
    /** @var int  */
    private $lines = 0;

    /** @var int  */
    private $files = 0;

    /** @var  Finder */
    private $finder;

    /** @var array  */
    private $extensions = ['php', 'twig', 'yml', 'html'];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('netbs:core:status');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root           = $this->getContainer()->getParameter('kernel.root_dir') . "/../src";
        $this->finder   = new Finder();
        $files          = $this->finder->files()->in($root);
        $io             = new SymfonyStyle($input, $output);

        $io->writeln("Calcul du nombre de lignes dans src/");
        $io->progressStart(count($files));

        /** @var SplFileInfo $file */
        foreach($files as $file) {

            $io->progressAdvance();

            if(in_array($file->getExtension(), $this->extensions)) {

                $this->files++;

                $handle = fopen($file->getRealPath(), 'r');
                while(!feof($handle)) {
                    $line = fgets($handle);
                    if(trim($line) != "")
                        $this->lines++;
                }

                fclose($handle);
            }
        }

        $io->progressFinish();

        $io->success(array(
            "Parcours termine. Seul les fichiers avec les extensions suivantes ont ete analyses : " . implode(",", $this->extensions),
            "Nombre de fichiers trouves : " . count($files),
            "Nombre de fichiers scannes : " . $this->files,
            "Nombre de lignes de codes : " . $this->lines
        ));
    }

    protected function iterate(SplFileInfo $directory) {

        /** @var SplFileInfo $file */
        foreach($this->finder->files()->in($directory->getRealPath()) as $file) {
            $handle = fopen($file->getRealPath(), 'r');
            while(!feof($handle))
                $this->lines++;
        }

        foreach($this->finder->directories()->in($directory->getRealPath()) as $dir)
            $this->iterate($dir);
    }

}
