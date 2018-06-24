<?php

namespace GalerieBundle\Command;

use GalerieBundle\Exceptions\MappingException;
use GalerieBundle\Model\NCNode;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumeNextcloudTaskCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('netbs:galerie:consume')
            ->setDescription('Consumes some nextcloud tasks')
            ->addArgument("amount", InputArgument::OPTIONAL, "Nombre de tâches à traiter", 1);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger     = $this->getContainer()->get('netbs.logger');
        $amount     = intval($input->getArgument('amount'));
        $mapper     = $this->getContainer()->get('galerie.mapper');
        $pheanstalk = $this->getContainer()->get('pheanstalk');
        $tubes      = $pheanstalk->listTubes();

        if(!in_array('netbs.galerie.waiting', $tubes))
            return;

        $stats      = $pheanstalk->statsTube('netbs.galerie.waiting');
        $amount     = $amount > intval($stats['current-jobs-ready']) ? intval($stats['current-jobs-ready']) : $amount;

        $first      = memory_get_usage();
        $previous   = $first;
        for($i = 0; $i < $amount; $i++) {

            $job = $pheanstalk->watch('netbs.galerie.waiting')
                ->ignore('default')
                ->reserve();

            dump($job->getData());

            $data           = json_decode($job->getData(), true);
            $node           = new NCNode($data);

            try {
                $mapper->handle($data['type'], $node);
            } catch (MappingException $e) {
                $logger->logUsername($data['username'], $e->getLevel(), $e->getMessage());
            }

            $pheanstalk->delete($job);

            $currentMemory  = memory_get_usage();
            $output->writeln("Iteration $i");
            $output->writeln("current memory usage: " . $this->printMemory($currentMemory));
            $output->writeln("memory gap from beginning: " . $this->printMemory($currentMemory - $first));
            $output->writeln("memory gap from before: " . $this->printMemory($currentMemory - $previous));
            $output->writeln("------------------\n");
            $previous   = $currentMemory;
        }
    }

    protected function printMemory($size) {

        if ($size < 1024)
            return $size." b";
        elseif ($size < 1048576)
            return round($size/1024,2)." kb";
        else
            return round($size/1048576,2)." mb";
    }
}
