<?php

namespace NetBS\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('netbs:install')
            ->setDescription('Primary install script')
            ->addOption('purge', null, InputOption::VALUE_OPTIONAL, 'If set to true, purge database')
            ->addOption('dummy', null, InputOption::VALUE_OPTIONAL, 'If set to true, loads some dummy data')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $purge      = $this->getBoolValue($input->getOption('purge'));
        $dummy      = $this->getBoolValue($input->getOption('dummy'));

        $io         = new SymfonyStyle($input, $output);
        $io->title("NetBS installation tool");
        $io->writeln("Welcome to the NetBS installation wizard.");

        $purge      = $purge === null
            ? $io->ask("Would you like to purge any current database ? [y/n]", 'y') == 'y'
            : $purge;

        if($purge) {
            $this->getApplication()->find('doctrine:database:drop')->run(new ArrayInput(['--force' => true]), $output);
            $this->getApplication()->find('doctrine:database:create')->run(new ArrayInput([]), $output);
            $this->getApplication()->find('doctrine:schema:update')->run(new ArrayInput(['--force' => true]), $output);
        }

        $io->writeln("Loading fixtures");
        $this->getApplication()->find('doctrine:fixtures:load')->run(new ArrayInput(['--append' => true]), $output);

        $dummy      = $dummy === null
            ? $io->ask("Would you like to load some dummy data? [y/n]", 'y') == 'y'
            : $dummy;

        if($dummy)
            $this->loadDummyData($io, $output);

        $scripts    = $this->getContainer()->get('netbs.core.post_install_script_manager')->getScripts();
        if(count($scripts))
            $io->writeln("Running post install scripts");

        /** @var Command $script */
        foreach($scripts as $script) {

            $io->writeln("Running " . $script->getName() . "...");
            $this->getApplication()->find($script->getName())->run(new ArrayInput([]), $output);
        }

        $io->writeln("Publishing assets");
        $this->getApplication()->find('assets:install')->run(new ArrayInput([]), $output);

        $io->writeln("clearing cache");
        $this->getApplication()->find('cache:clear')->run(new ArrayInput([]), $output);
    }

    protected function loadDummyData(SymfonyStyle $io, OutputInterface $output) {

        $bundles = $this->getContainer()->get('kernel')->getBundles();

        foreach($bundles as $bundle) {

            $path   = $bundle->getPath() . "/DataFixtures/Fill";
            if(is_dir($path)) {
                $this->getApplication()->find('doctrine:fixtures:load')->run(new ArrayInput([
                    '--append'      => true,
                    '--fixtures'    => $path
                ]), $output);
            }
        }
    }

    protected function getBoolValue($val) {

        if($val === null)
            return null;

        if($val === "false" || $val === "0")
            return false;

        return true;
    }
}
